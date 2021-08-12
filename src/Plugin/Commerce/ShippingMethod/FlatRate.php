<?php
namespace Drupal\honeys_place\Plugin\Commerce\ShippingMethod;

use Drupal\commerce_price\Price;
use Drupal\commerce_shipping\Entity\ShipmentInterface;
use Drupal\commerce_shipping\ShippingRate;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides the FlatRate shipping method.
 *
 * @CommerceShippingMethod(
 *   id = "flat_rate",
 *   label = @Translation("Flat rate"),
 * )
 */
class FlatRate extends \Drupal\commerce_shipping\Plugin\Commerce\ShippingMethod\FlatRate
{
  public function buildConfigurationForm(array $form, FormStateInterface $form_state)
  {
    $form['honeys_place_shipping_code'] = [
      '#type' => 'textfield',
      '#title' => t('Honeys Place Shipping Code'),
      '#description' => t('Maps the shipping method to a Honeys Place shipping code.'),
      '#default_value' => isset($this->configuration['honeys_place_shipping_code'])
        ? $this->configuration['honeys_place_shipping_code']
        : ''
    ];
    return parent::buildConfigurationForm($form, $form_state);
  }

  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    parent::submitConfigurationForm($form, $form_state);

    if (!$form_state->getErrors()) {
      $values = $form_state->getValue($form['#parents']);
      $this->configuration['honeys_place_shipping_code'] = $values['honeys_place_shipping_code'];
    }
  }

  public function calculateRates(ShipmentInterface $shipment) {
    $rates = [];
    $rates[] = new ShippingRate([
      'shipping_method_id' => $this->parentEntity->id(),
      'service' => $this->services['default'],
      'amount' => Price::fromArray($this->configuration['rate_amount']),
      'description' => $this->configuration['rate_description'],
      'honeys_place_shipping_code' => $this->configuration['honeys_place_shipping_code']
    ]);

    return $rates;
  }
}
