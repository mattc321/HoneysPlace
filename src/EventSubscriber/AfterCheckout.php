<?php
namespace Drupal\honeys_place\EventSubscriber;

use Drupal\commerce_checkout\Event\CheckoutEvents;
use Drupal\commerce_order\Entity\Order;
use Drupal\commerce_order\Event\OrderEvent;
use Drupal\commerce_order\Event\OrderEvents;
use Drupal\Core\Entity\EntityStorageException;
use Drupal\Core\Logger\LoggerChannelFactory;
use Drupal\honeys_place\Service\HoneyOrderManagementService;
use Exception;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Throwable;

class AfterCheckout implements EventSubscriberInterface
{
  /**
   * @var HoneyOrderManagementService
   */
  private $honeyOrderManagementService;
  /**
   * @var LoggerChannelFactory
   */
  private $loggerChannelFactory;

  /**
   * AfterCheckout constructor.
   * @param HoneyOrderManagementService $honeyOrderManagementService
   * @param LoggerChannelFactory $loggerChannelFactory
   */
  public function __construct(HoneyOrderManagementService $honeyOrderManagementService, LoggerChannelFactory $loggerChannelFactory)
  {
    $this->honeyOrderManagementService = $honeyOrderManagementService;
    $this->loggerChannelFactory = $loggerChannelFactory;
  }

  public function create(ContainerInterface $container)
  {
    return new static(
      $container->get('honey_order_management'),
      $container->get('logger.factory')
    );
  }

  /**
   * Returns an array of event names this subscriber wants to listen to.
   *
   * The array keys are event names and the value can be:
   *
   *  * The method name to call (priority defaults to 0)
   *  * An array composed of the method name to call and the priority
   *  * An array of arrays composed of the method names to call and respective
   *    priorities, or 0 if unset
   *
   * For instance:
   *
   *  * ['eventName' => 'methodName']
   *  * ['eventName' => ['methodName', $priority]]
   *  * ['eventName' => [['methodName1', $priority], ['methodName2']]]
   *
   * The code must not depend on runtime state as it will only be called at compile time.
   * All logic depending on runtime state must be put into the individual methods handling the events.
   *
   * @return array The event names to listen to
   */
  public static function getSubscribedEvents()
  {
    $events[CheckoutEvents::COMPLETION] = ['respondToCheckoutComplete', 0];
//    $events[OrderEvents::ORDER_LOAD] = ['respondToCheckoutComplete', 0]; //get status
    return $events;
  }

  /**
   * @param OrderEvent $event
   */
  public function respondToCheckoutComplete(OrderEvent $event)
  {
    try {
      $honeyOrder = $this->honeyOrderManagementService->createOrderInHoneysPlace($event->getOrder());
      if ($honeyOrder) {
        $order = Order::load($event->getOrder()->id());
        $order->set('field_honey_order_created', 1);
        $order->save();
      }
    } catch (Exception $e) {
      watchdog_exception('honeys_place', $e);
    } catch (Throwable $t) {
      $this->loggerChannelFactory->get('honeys_place')->error($t->getMessage());
    }
  }
}
