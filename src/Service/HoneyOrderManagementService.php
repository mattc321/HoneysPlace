<?php
namespace Drupal\honeys_place\Service;

use DateTime;
use Drupal\commerce_order\Entity\OrderInterface;
use Drupal\Core\Config\Config;
use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\Logger\LoggerChannelFactory;
use Drupal\honeys_place\Api\Request\Client;
use Drupal\honeys_place\Api\Request\Model\Order as HoneyOrder;
use Drupal\honeys_place\Api\Request\Model\OrderItem;
use Drupal\honeys_place\Api\Response\Model\CreateOrderResponse;
use Drupal\honeys_place\Api\Response\Model\OrderStatusResponse;
use Drupal\honeys_place\Api\Response\Model\StockStatusResponse;
use Drupal\honeys_place\Exception\ApiConnectionException;
use Drupal\honeys_place\Exception\MalformedEntityException;
use Drupal\honeys_place\Exception\MissingConfigurationException;
use GuzzleHttp\Exception\GuzzleException;

class HoneyOrderManagementService
{

    const DEFAULT_SHIP_CODE = 'P002';

    /**
   * @var LoggerChannelFactory
   */
  private $loggerFactory;
  /**
   * @var Client
   */
  private $client;
  /**
   * @var Config|ImmutableConfig
   */
  private $config;

  /**
   * HoneyOrderManagementService constructor.
   * @param LoggerChannelFactory $loggerFactory
   * @param Client $client
   * @param ConfigFactory $configFactory
   */
  public function __construct(
    LoggerChannelFactory $loggerFactory,
    Client $client,
    ConfigFactory $configFactory
  ) {
    $this->loggerFactory = $loggerFactory;
    $this->client = $client;
    $this->config = $configFactory->get('honeys_place.settings');
  }

  /**
   * @param OrderInterface $commerceOrder
   * @return CreateOrderResponse
   * @throws ApiConnectionException
   * @throws GuzzleException
   * @throws MalformedEntityException
   * @throws MissingConfigurationException
   */
  public function createOrderInHoneysPlace(OrderInterface $commerceOrder)
  {
    $this->validateOrder($commerceOrder);

    $address = $commerceOrder->getBillingProfile()->address->getValue()[0];

    $isSandboxMode = $this->config->get('honeys_place_api_use_sandbox');

    $honeysPlaceShippingCode = $this->getHoneysPlaceShippingCode($commerceOrder);

    $orderRequest = new HoneyOrder(
      $isSandboxMode ? 'TEST'.$commerceOrder->getOrderNumber() : $commerceOrder->getOrderNumber(),
      $honeysPlaceShippingCode,
      new DateTime(),
      $this->convertOrderItems($commerceOrder),
      $address['given_name'],
      $address['family_name'],
      $address['address_line1'],
      isset($address['address_line2']) ? $address['address_line2'] : '',
      $address['locality'],
      $address['administrative_area'],
      $address['postal_code'],
      $address['country_code'],
      isset($address['telephone']) ? $address['telephone'] : '',
      $commerceOrder->getEmail(),
      $address['organization'],
      'packing_slip_one' //get it from config
    );

    $response = $this->client->createOrder($orderRequest);

    return $response;
  }

  /**
   * @param string|int $orderNumber
   * @return OrderStatusResponse
   * @throws ApiConnectionException
   * @throws GuzzleException
   * @throws MissingConfigurationException
   */
  public function getHoneyOrderStatus($orderNumber)
  {
    $isSandboxMode = $this->config->get('honeys_place_api_use_sandbox');
    $orderNumber = $isSandboxMode ? 'TEST'.$orderNumber : $orderNumber;
    return $this->client->getOrderStatus($orderNumber);
  }

  /**
   * @param int|string $sku
   * @return StockStatusResponse
   * @throws ApiConnectionException
   * @throws GuzzleException
   * @throws MissingConfigurationException
   */
  public function getHoneyStockStatus($sku)
  {
    return $this->client->getStockStatus($sku);
  }

  /**
   * @param OrderInterface $commerceOrder
   * @return array
   */
  private function convertOrderItems(OrderInterface $commerceOrder): array
  {
    $items = [];
    foreach ($commerceOrder->getItems() as $item) {

      if (! $item->getPurchasedEntity()->hasField('sku') || ! $item->getPurchasedEntity()->sku->value) {
        $this->loggerFactory->get('honeys_place')->error("Could not convert order item on honey order request. Missing SKU.");
        continue;
      }

      $items[] = new OrderItem($item->getPurchasedEntity()->sku->value, $item->getQuantity());
    }

    return $items;
  }

  /**
   * @param OrderInterface $commerceOrder
   * @throws MalformedEntityException
   */
  private function validateOrder(OrderInterface $commerceOrder): void
  {
    if (! $commerceOrder->getBillingProfile() || ! $commerceOrder->getBillingProfile()->hasField('address')
      || ! $commerceOrder->getBillingProfile()->address->getValue()) {
      throw new MalformedEntityException('No address information provided');
    }

    $addressArray = $commerceOrder->getBillingProfile()->address->getValue()[0];

    $requiredFields = [
      'country_code',
      'administrative_area',
      'locality',
      'postal_code',
      'address_line1',
      'given_name'
    ];

    foreach ($requiredFields as $requiredField) {
      if (! isset($addressArray[$requiredField]) || ! $addressArray[$requiredField]) {
        throw new MalformedEntityException("Missing required field {$requiredField}");
      }
    }
  }

    /**
     * @param OrderInterface $commerceOrder
     * @return string
     */
    private function getHoneysPlaceShippingCode(OrderInterface $commerceOrder)
    {

        try {
            $pluginConfig = $commerceOrder->shipments->entity->shipping_method->entity->plugin->getValue()[0];

            if (isset($pluginConfig['target_plugin_configuration']) && isset($pluginConfig['target_plugin_configuration']['honeys_place_shipping_code'])) {
                return $pluginConfig['target_plugin_configuration']['honeys_place_shipping_code'] ?? self::DEFAULT_SHIP_CODE;
            }
        } catch (\Throwable $t) {
            $this->loggerFactory->get('honeys_place')->error("Could not get Honeys Place shipping code. Error: {$t->getMessage()}");
        }

        return self::DEFAULT_SHIP_CODE;
    }

}
