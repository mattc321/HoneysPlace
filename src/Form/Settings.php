<?php
namespace Drupal\honeys_place\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\Entity\EntityFormDisplay;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Messenger\Messenger;
use Drupal\honeys_place\Service\HoneyOrderManagementService;
use Drupal\profile\Entity\Profile;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Throwable;

class Settings extends ConfigFormBase
{
  /**
   * @var HoneyOrderManagementService
   */
  protected $honeyOrderManagementService;

  /**
   * Settings constructor.
   * @param ConfigFactoryInterface $config_factory
   * @param Messenger $messenger
   * @param HoneyOrderManagementService $honeyOrderManagementService
   */
  public function __construct(
    ConfigFactoryInterface $config_factory,
    Messenger $messenger,
    HoneyOrderManagementService $honeyOrderManagementService
  ) {
    $this->messenger = $messenger;
    $this->honeyOrderManagementService = $honeyOrderManagementService;
    parent::__construct($config_factory);
  }

  /**
   * @param ContainerInterface $container
   * @return ConfigFormBase|Settings
   */
  public static function create(ContainerInterface $container)
  {
    return new static(
      $container->get('config.factory'),
      $container->get('messenger'),
      $container->get('honeys_place.honey_order_management')
    );
  }

  /**
   * Gets the configuration names that will be editable.
   *
   * @return array
   *   An array of configuration object names that are editable if called in
   *   conjunction with the trait's config() method.
   */
  protected function getEditableConfigNames()
  {
    return [
      'honeys_place.settings',
    ];
  }

  /**
   * Returns a unique string identifying the form.
   *
   * The returned ID should be a unique string that can be a valid PHP function
   * name, since it's used in hook implementation names such as
   * hook_form_FORM_ID_alter().
   *
   * @return string
   *   The unique string identifying the form.
   */
  public function getFormId()
  {
    return 'honeys_place_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('honeys_place.settings');
    $form = parent::buildForm($form, $form_state);
    $form['container'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => 'settings-container',
        'id' => 'settings-wrapper'
      ],
    ];
    $form['container']['honeys_place_api_endpoint'] = [
      '#type' => 'textfield',
      '#title' => $this->t('API Endpoint'),
      '#default_value' => $config->get('honeys_place_api_endpoint')
    ];
    $form['container']['honeys_place_api_username'] = [
      '#type' => 'textfield',
      '#title' => $this->t('API Username'),
      '#default_value' => $config->get('honeys_place_api_username')
    ];
    $form['container']['honeys_place_api_password'] = [
      '#type' => 'password',
      '#title' => $this->t('API Password'),
      '#default_value' => $config->get('honeys_place_api_password'),
      '#suffix' => $config->get('honeys_place_api_password')
        ? '<div class="messages messages--status">API password is set</div>'
        : ''
    ];
    $form['container']['response'] = [
      '#type' => 'markup',
      '#markup' => '<div class="test-response"></div>',
    ];
    $form['container']['honeys_place_api_test'] = [
      '#type' => 'button',
      '#value' => $this->t('Test API Connection'),
      '#attributes' => [
        'class' => [
          'use-ajax'
        ]
      ],
      '#ajax' => [
        'callback' => '::testApiConnection',
        'wrapper' => 'honeys-place-settings-form'
      ]
    ];
    $form['container']['honeys_place_api_use_sandbox'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Use Sandbox'),
      '#default_value' => $config->get('honeys_place_api_use_sandbox'),
      '#suffix' => $config->get('honeys_place_api_use_sandbox')
        ? '<em>Sandbox Mode Enabled</em>'
        : '<em>Production Mode Enabled</em>'
    ];

    $profile = Profile::create(['type' => 'customer']);
    $customerForm = EntityFormDisplay::collectRenderDisplay($profile, 'default');

    if (! $customerForm->getComponent('address')) {
      $this->messenger->addError('Missing address field on customer profile.');
    }

    return $form;
  }

  /**
   * @param array $form
   * @param FormStateInterface $form_state
   * @return array|AjaxResponse
   */
  public function testApiConnection(array &$form, FormStateInterface $form_state)
  {
    $response = new AjaxResponse();
    $response->addCommand(new HtmlCommand(
      '.test-response',
      $this->getTestApiResponseOutput()
      )
    );
    return $response;
  }

  /**
   * @param $name
   * @return bool
   */
  protected function isOverridden($name) {
    $original = $this->configFactory->getEditable('honeys_place.settings')->get($name);
    $current = $this->configFactory->get('honeys_place.settings')->get($name);
    return $original != $current;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $config = $this->config('honeys_place.settings');
    $config->set('honeys_place_api_endpoint', $form_state->getValue('honeys_place_api_endpoint'));
    $config->set('honeys_place_api_username', $form_state->getValue('honeys_place_api_username'));
    $config->set('honeys_place_api_use_sandbox', $form_state->getValue('honeys_place_api_use_sandbox'));
    if ($form_state->getValue('honeys_place_api_password') && !$this->isOverridden('honeys_place_api_password')) {
      $config->set('honeys_place_api_password', $form_state->getValue('honeys_place_api_password'));
    }
    $config->save();
  }

  /**
   * @return string
   */
  private function getTestApiResponseOutput()
  {
    try {
      $response = $this->honeyOrderManagementService->getHoneyOrderStatus('TESTxxxx');
    } catch (Throwable $t) {
      return "<div class=\"messages messages--error\"><div>Exception occurred: {$t->getMessage()}</div></div>";
    }

    return ($response->getStatus())
      ? '<div class="messages messages--status"><div>Connection successful!</div></div>'
      : "<div class=\"messages messages--error\"><div>
              Connection failed. 
              Code: {$response->getCode()} 
              Error: {$response->getError()} 
              Details: {$response->getDetails()}
        </div></div>";
  }

}
