<?php
namespace Drupal\honeys_place\Api\Request;

use Drupal\Core\Config\Config;
use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Config\ImmutableConfig;
use Drupal\honeys_place\Api\Response\Model\CreateOrderResponse;
use Drupal\honeys_place\Api\Response\Model\OrderStatusResponse;
use Drupal\honeys_place\Api\Response\Model\ResponseInterface;
use Drupal\honeys_place\Api\Response\Model\StockStatusResponse;
use Drupal\honeys_place\Exception\ApiConnectionException;
use Drupal\honeys_place\Exception\MissingConfigurationException;
use GuzzleHttp\Client as GuzzleClient;
use Drupal\honeys_place\Api\Request\Model\Order;
use Drupal\honeys_place\Api\Response\ResponseHandler;
use GuzzleHttp\Exception\GuzzleException;

class Client
{
  /**
   * @var ResponseHandler
   */
  private $responseHandler;
  /**
   * @var Config|ImmutableConfig
   */
  private $config;

  /**
   * Client constructor.
   * @param ResponseHandler $responseHandler
   * @param ConfigFactory $configFactory
   */
  public function __construct(ResponseHandler $responseHandler, ConfigFactory $configFactory)
  {
    $this->responseHandler = $responseHandler;
    $this->config = $configFactory->get('honeys_place.settings');
  }

  /**
   * @param $orderNumber
   * @return OrderStatusResponse|ResponseInterface
   * @throws ApiConnectionException
   * @throws GuzzleException
   * @throws MissingConfigurationException
   */
  public function getOrderStatus($orderNumber)
  {
    $this->validateConfig();

    $xml = '<?xml version="1.0" encoding="UTF-8"?>
     <HPEnvelope>
     <account>'.$this->config->get('honeys_place_api_username').'</account>
     <password>'.$this->config->get('honeys_place_api_password').'</password>
     <orderstatus>'.$orderNumber.'</orderstatus>
     </HPEnvelope>';

    return $this->request($xml, OrderStatusResponse::class);
  }

  /**
   * @param Order $orderRequest
   * @return CreateOrderResponse|ResponseInterface
   * @throws ApiConnectionException
   * @throws GuzzleException
   * @throws MissingConfigurationException
   */
  public function createOrder(Order $orderRequest)
  {

    $this->validateConfig();

    $xml = '<?xml version="1.0" encoding="UTF-8"?>
            <HPEnvelope>
            <account>'.$this->config->get('honeys_place_api_username').'</account>
            <password>'.$this->config->get('honeys_place_api_password').'</password>
            <order>
              <reference>'.$orderRequest->getOrderNumber().'</reference>
              <shipby>'.$orderRequest->getShipBy().'</shipby>
              <date>'.$orderRequest->getDate()->format('m/d/Y').'</date>
              <items>';
              foreach ($orderRequest->getItems() as $item) {
                $xml .= '<item>
                          <sku>'.$item->getSku().'</sku>
                          <qty>'.$item->getQty().'</qty>
                        </item>';
              }
      $xml .= '</items>
              <last>'.$orderRequest->getLastName().'</last>
              <first>'.$orderRequest->getFirstName().'</first>
              <address1>'.$orderRequest->getAddress1().'</address1>
              <address2>'.$orderRequest->getAddress2().'</address2>
              <city>'.$orderRequest->getCity().'</city>
              <state>'.$orderRequest->getState().'</state>
              <zip>'.$orderRequest->getZip().'</zip>
              <country>'.$orderRequest->getCountry().'</country>
              <phone>'.$orderRequest->getPhone().'</phone>
              <emailaddress>'.$orderRequest->getEmailAddress().'</emailaddress>
              <instructions>'.$orderRequest->getInstructions().'</instructions>
              <packingslip_name>'.$orderRequest->getPackingSlip().'</packingslip_name>
            </order>
          </HPEnvelope>';

    return $this->request($xml, CreateOrderResponse::class);
  }

  /**
   * @param string $sku
   * @return StockStatusResponse|ResponseInterface
   * @throws ApiConnectionException
   * @throws GuzzleException
   * @throws MissingConfigurationException
   */
  public function getStockStatus(string $sku)
  {

    $this->validateConfig();

      $xml = '<?xml version="1.0" encoding="UTF-8"?>
          <HPEnvelope>
          <account>'.$this->config->get('honeys_place_api_username').'</account>
          <password>'.$this->config->get('honeys_place_api_password').'</password>
          <stockcheck>
           <sku>'.$sku.'</sku>
           </stockcheck>
        </HPEnvelope>';

      return $this->request($xml, StockStatusResponse::class);

  }

  /**
   * @param string $xml
   * @param string $responseModel
   * @return ResponseInterface
   * @throws ApiConnectionException
   * @throws GuzzleException
   */
  private function request(string $xml, string $responseModel)
  {
    $client = new GuzzleClient();

    $response = $client->request(
      'GET',
      $this->config->get('honeys_place_api_endpoint'),
      [
        'query' => [
          'xmldata' => $xml
        ]
      ]
    );

    return $this->responseHandler->getHandledResponse($response, $responseModel);
  }

  /**
   * @throws MissingConfigurationException
   */
  private function validateConfig()
  {
    if (! $this->config->get('honeys_place_api_endpoint')
      || ! $this->config->get('honeys_place_api_username')
      || ! $this->config->get('honeys_place_api_password'))
    {
      throw new MissingConfigurationException('Missing configuration. Cannot connect to Honeys Place.');
    }
  }
}
