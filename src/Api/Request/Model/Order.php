<?php
namespace Drupal\honeys_place\Api\Request\Model;

use DateTime;

class Order
{
  /**
   * @var string
   */
  private $orderNumber;
  /**
   * @var string
   */
  private $shipBy;
  /**
   * @var DateTime
   */
  private $date;
  /**
   * @var OrderItem[]
   */
  private $items;
  /**
   * @var string
   */
  private $firstName;
  /**
   * @var string
   */
  private $lastName;
  /**
   * @var string
   */
  private $address1;
  /**
   * @var string|null
   */
  private $address2;
  /**
   * @var string
   */
  private $city;
  /**
   * @var string
   */
  private $state;
  /**
   * @var string
   */
  private $zip;
  /**
   * @var string
   */
  private $country;
  /**
   * @var string
   */
  private $phone;
  /**
   * @var string
   */
  private $emailAddress;
  /**
   * @var string
   */
  private $instructions;
  /**
   * @var string
   */
  private $packingSlip;

  /**
   * Order constructor.
   * @param string $orderNumber
   * @param string $shipBy
   * @param DateTime $date
   * @param OrderItem[] $items
   * @param string $firstName
   * @param string $lastName
   * @param string $address1
   * @param string|null $address2
   * @param string $city
   * @param string $state
   * @param string $zip
   * @param string $country
   * @param string $phone
   * @param string $emailAddress
   * @param string $instructions
   * @param string $packingSlip
   */
  public function __construct(
    string $orderNumber,
    string $shipBy,
    DateTime $date,
    array $items,
    string $firstName,
    string $lastName,
    string $address1,
    ?string $address2,
    string $city,
    string $state,
    string $zip,
    string $country,
    string $phone,
    string $emailAddress,
    string $instructions,
    string $packingSlip
  ) {
    $this->orderNumber = $orderNumber;
    $this->shipBy = $shipBy;
    $this->date = $date;
    $this->items = $items;
    $this->firstName = $firstName;
    $this->lastName = $lastName;
    $this->address1 = $address1;
    $this->address2 = $address2;
    $this->city = $city;
    $this->state = $state;
    $this->zip = $zip;
    $this->country = $country;
    $this->phone = $phone;
    $this->emailAddress = $emailAddress;
    $this->instructions = $instructions;
    $this->packingSlip = $packingSlip;
  }

  /**
   * @return string
   */
  public function getOrderNumber(): string
  {
    return $this->orderNumber;
  }

  /**
   * @return string
   */
  public function getShipBy(): string
  {
    return $this->shipBy;
  }

  /**
   * @return DateTime
   */
  public function getDate(): DateTime
  {
    return $this->date;
  }

  /**
   * @return OrderItem[]
   */
  public function getItems(): array
  {
    return $this->items;
  }

  /**
   * @return string
   */
  public function getFirstName(): string
  {
    return $this->firstName;
  }

  /**
   * @return string
   */
  public function getLastName(): string
  {
    return $this->lastName;
  }

  /**
   * @return string
   */
  public function getAddress1(): string
  {
    return $this->address1;
  }

  /**
   * @return string|null
   */
  public function getAddress2(): ?string
  {
    return $this->address2;
  }

  /**
   * @return string
   */
  public function getCity(): string
  {
    return $this->city;
  }

  /**
   * @return string
   */
  public function getState(): string
  {
    return $this->state;
  }

  /**
   * @return string
   */
  public function getZip(): string
  {
    return $this->zip;
  }

  /**
   * @return string
   */
  public function getCountry(): string
  {
    return $this->country;
  }

  /**
   * @return string
   */
  public function getPhone(): string
  {
    return $this->phone;
  }

  /**
   * @return string
   */
  public function getEmailAddress(): string
  {
    return $this->emailAddress;
  }

  /**
   * @return string
   */
  public function getInstructions(): string
  {
    return $this->instructions;
  }

  /**
   * @return string
   */
  public function getPackingSlip(): string
  {
    return $this->packingSlip;
  }
}
