<?php

/**
 * @file
 * Contains sales_entity.page.inc.
 *
 * Page callback for Sales entity entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Sales entity templates.
 *
 * Default template: sales_entity.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_sales_entity(array &$variables) {
  // Fetch SalesEntity Entity Object.
  $sales_entity = $variables['elements']['#sales_entity'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
