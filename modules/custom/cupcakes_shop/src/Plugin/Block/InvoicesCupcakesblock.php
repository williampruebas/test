<?php

namespace Drupal\cupcakes_shop\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a 'SalesCupcakesblock' block.
 *
 * @Block(
 *  id = "invoices_cupcakesblock",
 *  admin_label = @Translation("Invoices cupcakes block"),
 * )
 */
class InvoicesCupcakesblock extends BlockBase {
  

  /**
   * {@inheritdoc}
   */
  public function build() {
    $service = \Drupal::service('cupcakes_shop.invoice_service');
    $table = $service->getTable();
    $file = $service->getFile();
    $build = [];
    $build['#theme'] = 'invoice_cupcakes';
    $build['#data'] = [
      'table' => $table,
      'file' => $file['url'],
    ];
    $build['#attached'] = [
      'library' => [
        'cupcakes_shop/invoices-cupcakes',
      ],
    ];
  
    return $build;
  }

}
