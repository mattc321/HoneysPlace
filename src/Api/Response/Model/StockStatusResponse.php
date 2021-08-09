<?php


namespace Drupal\honeys_place\Api\Response\Model;


class StockStatusResponse implements ResponseInterface
{
  /**
   * @var array
   */
  private $data;
  /**
   * @var string
   */
  private $sku;
  /**
   * @var int
   */
  private $qty;
  /**
   * @var bool
   */
  private $isInStock;
  /**
   * @var string
   */
  private $code;
  /**
   * @var string
   */
  private $details;

  /**
   * StockStatusResponse constructor.
   * @param string $sku
   * @param int $qty
   * @param bool $isInStock
   * @param array $data
   * @param string $code
   * @param string $details
   */
  public function __construct(string $sku, int $qty, bool $isInStock, array $data = [], string $code = '', string $details = '')
  {
    $this->sku = $sku;
    $this->qty = $qty;
    $this->isInStock = $isInStock;
    $this->data = $data;
    $this->code = $code;
    $this->details = $details;
  }

  /**
   * @return array
   */
  public function getData(): array
  {
    return $this->data;
  }

  /**
   * @return string
   */
  public function getSku(): string
  {
    return $this->sku;
  }

  /**
   * @return int
   */
  public function getQty(): string
  {
    return $this->qty;
  }

  /**
   * @return bool
   */
  public function isInStock(): bool
  {
    return $this->isInStock;
  }

  /**
   * @return string
   */
  public function getCode(): string
  {
    return $this->code;
  }

  /**
   * @return string
   */
  public function getDetails(): string
  {
    return $this->details;
  }
}
