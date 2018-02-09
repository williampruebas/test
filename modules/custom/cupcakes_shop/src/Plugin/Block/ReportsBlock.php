<?php

namespace Drupal\cupcakes_shop\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Provides a 'SalesCupcakesblock' block.
 *
 * @Block(
 *  id = "reports_block",
 *  admin_label = @Translation("Reports block"),
 * )
 */
class ReportsBlock extends BlockBase {
  
  /**
   * {@inheritdoc}
   */
  public function build() {
    \Drupal::service('page_cache_kill_switch')->trigger();
    $service = \Drupal::service('cupcakes_shop.invoice_service');
  
    $byMonth =  $service->reportByMonth();
    $byClient =  $service->reportByClient();
    $byCupcake =  $service->reportByCupCake();
    
    $build = [];
    $build['sales_cupcakesblock']['#markup'] = 'Implement SalesCupcakesblock.';
    $build['#theme'] = 'report_cupcakes';
    $build['#data'] = [
      'month' => $byMonth['url'],
      'client' => $byClient['url'],
      'cupcakes' => $byCupcake['url'],
    ];
  
    return $build;
  }
  
  public function getCacheMaxAge(){
    return 0;
  }

}
