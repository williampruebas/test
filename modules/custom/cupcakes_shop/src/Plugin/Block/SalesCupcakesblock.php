<?php

namespace Drupal\cupcakes_shop\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a 'SalesCupcakesblock' block.
 *
 * @Block(
 *  id = "sales_cupcakesblock",
 *  admin_label = @Translation("Sales cupcakes block"),
 * )
 */
class SalesCupcakesblock extends BlockBase {
  
  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'url' => $GLOBALS['base_url'].'/buy-cupcakes',
    ];
  }
  
  /**
   * {@inheritdoc}
   */
  
  public function blockForm($form, FormStateInterface $form_state) {
    $form['#tree'] = TRUE;
    
    $form['container'] = [
      '#type' => 'details',
      '#title' => $this->t('Configurations'),
      '#open' => TRUE,
    ];
    
    $form['container']['url'] = [
      '#type' => 'url',
      '#title' => $this->t('URL to confirm sale'),
      '#size' => 30,
      '#default_value' => $this->configuration['url'],
    ];
    
    return $form;
  }
  
  /**
   * {@inheritdoc}
   */
  
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['url'] = $form_state->getValue('container')['url'];
  }
  
  /**
   * {@inheritdoc}
   */

  /**
   * {@inheritdoc}
   */
  public function build() {
    \Drupal::service('page_cache_kill_switch')->trigger();
    $count = 0;
    foreach ($_SESSION['cupcakes'] as $type) {
      $count+= count($type);
    }
    
    $build = [];
    $build['sales_cupcakesblock']['#markup'] = 'Implement SalesCupcakesblock.';
    $build['#theme'] = 'sale_cupcakes';
    $build['#data'] = [
      'url' => $this->configuration['url'],
      'count' => $count,
    ];
    $build['#attached'] = [
      'library' => [
        'cupcakes_shop/car-cupcakes',
      ],
    ];
  
    return $build;
  }
  
  public function getCacheMaxAge(){
    return 0;
  }
}
