<?php

namespace Drupal\cupcakes_shop\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Sales entity entities.
 *
 * @ingroup cupcakes_shop
 */
interface SalesEntityInterface extends ContentEntityInterface, EntityChangedInterface, EntityOwnerInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the Sales entity name.
   *
   * @return string
   *   Name of the Sales entity.
   */
  public function getName();

  /**
   * Sets the Sales entity name.
   *
   * @param string $name
   *   The Sales entity name.
   *
   * @return \Drupal\cupcakes_shop\Entity\SalesEntityInterface
   *   The called Sales entity entity.
   */
  public function setName($name);

  /**
   * Gets the Sales entity creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Sales entity.
   */
  public function getCreatedTime();

  /**
   * Sets the Sales entity creation timestamp.
   *
   * @param int $timestamp
   *   The Sales entity creation timestamp.
   *
   * @return \Drupal\cupcakes_shop\Entity\SalesEntityInterface
   *   The called Sales entity entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Sales entity published status indicator.
   *
   * Unpublished Sales entity are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Sales entity is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Sales entity.
   *
   * @param bool $published
   *   TRUE to set this Sales entity to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\cupcakes_shop\Entity\SalesEntityInterface
   *   The called Sales entity entity.
   */
  public function setPublished($published);

}
