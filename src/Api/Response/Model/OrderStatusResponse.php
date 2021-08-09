<?php
namespace Drupal\honeys_place\Api\Response\Model;


class OrderStatusResponse implements ResponseInterface
{
  /**
   * @var string
   */
  private $referenceOrder;
  /**
   * @var string
   */
  private $salesOrder;
  /**
   * @var string
   */
  private $orderDate;
  /**
   * @var string
   */
  private $shipAgent;
  /**
   * @var string
   */
  private $shipService;
  /**
   * @var string
   */
  private $freightCost;
  /**
   * @var string
   */
  private $status;
  /**
   * @var array
   */
  private $trackingNumbers;
  /**
   * @var array
   */
  private $data;

  /**
   * TrackCreateOrderResponse constructor.
   * @param string $referenceOrder
   * @param string $salesOrder
   * @param string $orderDate
   * @param string $shipAgent
   * @param string $shipService
   * @param string $freightCost
   * @param string $status
   * @param array $trackingNumbers
   * @param array $data
   */
  public function __construct(
      string $referenceOrder,
      string $salesOrder,
      string $orderDate,
      string $shipAgent,
      string $shipService,
      string $freightCost,
      string $status,
      array $trackingNumbers = [],
      array $data = []
    ) {
    $this->referenceOrder = $referenceOrder;
    $this->salesOrder = $salesOrder;
    $this->orderDate = $orderDate;
    $this->shipAgent = $shipAgent;
    $this->shipService = $shipService;
    $this->freightCost = $freightCost;
    $this->status = $status;
    $this->trackingNumbers = $trackingNumbers;
    $this->data = $data;
  }

  /**
   * @return string
   */
  public function getReferenceOrder(): string
  {
    return $this->referenceOrder;
  }

  /**
   * @return string
   */
  public function getSalesOrder(): string
  {
    return $this->salesOrder;
  }

  /**
   * @return string
   */
  public function getOrderDate(): string
  {
    return $this->orderDate;
  }

  /**
   * @return string
   */
  public function getShipAgent(): string
  {
    return $this->shipAgent;
  }

  /**
   * @return string
   */
  public function getShipService(): string
  {
    return $this->shipService;
  }

  /**
   * @return string
   */
  public function getFreightCost(): string
  {
    return $this->freightCost;
  }

  /**
   * @return string
   */
  public function getStatus(): string
  {
    return $this->status;
  }

  /**
   * @return array
   */
  public function getTrackingNumbers(): array
  {
    return $this->trackingNumbers;
  }

  /**
   * @return array
   */
  public function getData(): array
  {
    return $this->data;
  }

}
