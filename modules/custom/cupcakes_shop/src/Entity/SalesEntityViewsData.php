<?php

namespace Drupal\cupcakes_shop\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Sales entity entities.
 */
class SalesEntityViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    // Additional information for Views integration, such as table joins, can be
    // put here.

    return $data;
  }

}
