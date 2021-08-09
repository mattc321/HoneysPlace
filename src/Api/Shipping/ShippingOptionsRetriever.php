<?php


class ShippingOptionsRetriever
{
  const CARRIER_DHL = 'DHL';
  const CARRIER_FEDEX = 'fedex';
  const CARRIER_USPS = 'usps';
  const CARRIER_UPS = 'ups';

  const SHIPPING_CODES = [
    self::CARRIER_DHL => [

    ],
    self::CARRIER_FEDEX => [

    ],
    self::CARRIER_USPS => [
      'P002' => 'Priority Mail'
    ],
    self::CARRIER_UPS => [

    ]
  ];


  public function getAllShippingOptionsByCarrier(string $carrier)
  {

  }
}
