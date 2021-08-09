<?php


namespace Drupal\honeys_place\Api\Response\Model;


class GeneralResponse implements ResponseInterface
{
  /**
   * @var array
   */
  private $data;

  /**
   * GeneralResponse constructor.
   * @param array $data
   */
  public function __construct(array $data = [])
  {
    $this->data = $data;
  }

  /**
   * @return array
   */
  public function getData(): array
  {
    return $this->data;
  }
}
