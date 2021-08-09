<?php
namespace Drupal\honeys_place\Api\Request\Model;

class OrderItem
{
  /**
   * @var string
   */
  private $sku;
  /**
   * @var float
   */
  private $qty;

  /**
   * OrderItem constructor.
   * @param string $sku
   * @param float $qty
   */
  public function __construct(string $sku, float $qty)
  {
    $this->sku = $sku;
    $this->qty = $qty;
  }

  /**
   * @return string
   */
  public function getSku(): string
  {
    return $this->sku;
  }

  /**
   * @return float
   */
  public function getQty(): float
  {
    return $this->qty;
  }
}
