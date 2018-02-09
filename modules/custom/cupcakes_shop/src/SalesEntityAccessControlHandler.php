<?php

namespace Drupal\cupcakes_shop;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Sales entity entity.
 *
 * @see \Drupal\cupcakes_shop\Entity\SalesEntity.
 */
class SalesEntityAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\cupcakes_shop\Entity\SalesEntityInterface $entity */
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished sales entity entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published sales entity entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit sales entity entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete sales entity entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add sales entity entities');
  }

}
