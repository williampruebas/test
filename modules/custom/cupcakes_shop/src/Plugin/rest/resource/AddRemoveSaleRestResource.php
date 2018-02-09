<?php

namespace Drupal\cupcakes_shop\Plugin\rest\resource;

use Drupal\Core\Session\AccountProxyInterface;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Psr\Log\LoggerInterface;
use Drupal\cupcakes_shop\Entity\SalesEntity;
/**
 * Provides a resource to get view modes by entity and bundle.
 *
 * @RestResource(
 *   id = "add_remove_sale_rest_resource",
 *   label = @Translation("Add remove sale rest resource"),
 *   uri_paths = {
 *     "canonical" = "/cupcake/sale"
 *   }
 * )
 */
class AddRemoveSaleRestResource extends ResourceBase {
  
  /**
   * A current user instance.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;
  
  /**
   * Constructs a new AddRemoveSaleRestResource object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param array $serializer_formats
   *   The available serialization formats.
   * @param \Psr\Log\LoggerInterface $logger
   *   A logger instance.
   * @param \Drupal\Core\Session\AccountProxyInterface $current_user
   *   A current user instance.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    array $serializer_formats,
    LoggerInterface $logger,
    AccountProxyInterface $current_user) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $serializer_formats, $logger);
    
    $this->currentUser = $current_user;
  }
  
  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->getParameter('serializer.formats'),
      $container->get('logger.factory')->get('cupcakes_shop'),
      $container->get('current_user')
    );
  }
  
  /**
   * Responds to GET requests.
   *
   * Returns a list of bundles for specified entity.
   *
   * @throws \Symfony\Component\HttpKernel\Exception\HttpException
   *   Throws exception expected.
   */
  public function get() {
    // Remove cache.
    \Drupal::service('page_cache_kill_switch')->trigger();
    
    // You must to implement the logic of your REST Resource here.
    // Use current user after pass authentication to validate access.
    if (!$this->currentUser->hasPermission('access content')) {
      throw new AccessDeniedHttpException();
    }
    
    $cupcakes = isset($_SESSION['cupcakes']) ? $_SESSION['cupcakes'] : [];
    
    switch ($_GET['type']) {
      case 'add':
        $cupcakes[$_GET['node']][] = $_GET['node'];
        break;
      
      case 'del':
        if (isset($cupcakes[$_GET['node']]) && count($cupcakes[$_GET['node']]) > 0) {
          $aux = $cupcakes[$_GET['node']];
          unset($aux[count($aux) - 1]);
          $cupcakes[$_GET['node']] = $aux;
        }
        break;
  
      case 'confirm':
        $this->saveSales();
        return new ResourceResponse ('ok');
        break;
    }
  
    $count = 0;
    foreach ($cupcakes as $type) {
      $count+= count($type);
    }
    
    $_SESSION['cupcakes'] = $cupcakes;
    
    return new ResourceResponse($count);
  }
  
  public function saveSales() {
    $invoice = time();

    $id = time();
    
    $uid = \Drupal::currentUser()->id();
  
    $values = [];
    
    foreach ($_SESSION['cupcakes'] as $key => $cupcakes) {
      $values [] = [
        'id' => $id,
        'amount' => count($cupcakes),
        'month' => date('F'),
        'invoice' => $invoice,
        'cupcake' => $key,
        'user_id'=> $uid,
      ];
    }
  
  
    $query = \Drupal::database()->insert('sales')->fields(['id', 'amount', 'month', 'invoice', 'cupcake', 'user_id']);
    foreach ($values as $record) {
      $query->values($record);
    }
    $query->execute();
    
    
    unset($_SESSION['cupcakes']);
    unset($_SESSION['table_data']);
  }
}
