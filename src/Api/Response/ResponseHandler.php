<?php
namespace Drupal\honeys_place\Api\Response;

use Drupal\Core\Logger\LoggerChannelFactory;
use Drupal\Core\Logger\LoggerChannelInterface;
use Drupal\honeys_place\Api\Response\Model\CreateOrderResponse;
use Drupal\honeys_place\Api\Response\Model\GeneralResponse;
use Drupal\honeys_place\Api\Response\Model\ResponseInterface as HoneyResponseInterface;
use Drupal\honeys_place\Api\Response\Model\OrderStatusResponse;
use Drupal\honeys_place\Api\Response\Model\StockStatusResponse;
use Drupal\honeys_place\Exception\ApiConnectionException;
use Psr\Http\Message\ResponseInterface;
use SimpleXMLElement;

class ResponseHandler
{

  /**
   * @var LoggerChannelInterface
   */
  private $logger;

  public function __construct(LoggerChannelFactory $loggerFactory)
  {
    $this->logger = $loggerFactory->get('honeys_place');
  }

  /**
   * @param ResponseInterface $response
   * @param string $responseModel
   * @return HoneyResponseInterface
   * @throws ApiConnectionException
   */
  public function getHandledResponse(ResponseInterface $response, string $responseModel): HoneyResponseInterface
  {
    if ($response->getStatusCode() !== 200) {

      $this->logger->error(
        'Could not create order in Honeys Place. Status: @status Error: @error',
        ['@status' => $response->getStatusCode(), '@error' => $response->getBody()]
      );

      throw new ApiConnectionException(
        "Could not connect to Honeys Place API. Response code: {$response->getStatusCode()}"
      );
    }

    $xml = new SimpleXMLElement($response->getBody());

    switch ($responseModel) {
      case CreateOrderResponse::class:
        return new CreateOrderResponse(
          (string)$xml->reference,
          (string)$xml->code,
          (string)$xml->details,
          (string)$xml->error,
          (array)$xml
        );
        break;
      case OrderStatusResponse::class:

        $tracking = [];
        if ((string)$xml->trackingnumber1) {
          $tracking[] = (string)$xml->trackingnumber1;
        }
        if ((string)$xml->trackingnumber2) {
          $tracking[] = (string)$xml->trackingnumber2;
        }
        if ((string)$xml->trackingnumber3) {
          $tracking[] = (string)$xml->trackingnumber3;
        }

        return new OrderStatusResponse(
          (string)$xml->reference,
          (string)$xml->salesorder,
          (string)$xml->orderdate,
          (string)$xml->shipagent,
          (string)$xml->shipservice,
          (string)$xml->freightcost,
          (string)$xml->status,
          $tracking,
          (array)$xml
        );
        break;

      case StockStatusResponse::class:
          return new StockStatusResponse(
            (string)$xml->stock->item->sku,
            (int)$xml->stock->item->qty,
            (int)$xml->stock->item->qty > 0,
            (array)$xml,
            (string)$xml->code,
            (string)$xml->details
      );
        break;
      default:

        return new GeneralResponse((array)$xml);

    }

  }
}
