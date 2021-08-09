<?php
namespace Drupal\honeys_place\Api\Response\Model;

class CreateOrderResponse implements ResponseInterface
{
  /**
   * @var string|int
   */
  private $referenceNumber;
  /**
   * @var string|int
   */
  private $code;
  /**
   * @var string
   */
  private $details;
  /**
   * @var bool
   */
  private $isSuccess;
  /**
   * @var string|null
   */
  private $error;
  /**
   * @var array
   */
  private $data;

  /**
   * Response constructor.
   * @param string|int $referenceNumber
   * @param string|int $code
   * @param string $details
   * @param string|null $error
   * @param array $data
   */
  public function __construct(string $referenceNumber, $code, string $details, string $error = null, array $data = [])
  {
    $this->referenceNumber = $referenceNumber;
    $this->code = $code;
    $this->setDetails($code, $details);
    $this->setRequestSuccessful($code);
    $this->error = $error;
    $this->data = $data;
  }

  /**
   * @return int|string
   */
  public function getReferenceNumber()
  {
    return $this->referenceNumber;
  }

  /**
   * @return int|string
   */
  public function getCode()
  {
    return $this->code;
  }

  /**
   * @return string
   */
  public function getDetails()
  {
    return $this->details;
  }

  /**
   * @return bool
   */
  public function isSuccess(): bool
  {
    return $this->isSuccess;
  }

  /**
   * @return string|null
   */
  public function getError(): ?string
  {
    return $this->error;
  }

  /**
   * @param int|string $code
   * @param string $details
   */
  private function setDetails($code, string $details)
  {
    //some codes contain the wholesaler name. Lets make it a little more friendly to the shop.
    switch ($code) {
      case 0:
          $details = 'An unknown error occurred. Please contact us.';
        break;
      case 999:
          $details = 'Could not connect to marketplace. Please contact us.';
        break;
    }
    $this->details = $details;
  }

  /**
   * @param int|string $code
   */
  private function setRequestSuccessful($code)
  {
    if ($code == 100) {
      $this->isSuccess = true;
      return;
    }
    $this->isSuccess = false;
  }

  /**
   * @return array
   */
  public function getData(): array
  {
    return $this->data;
  }
}
