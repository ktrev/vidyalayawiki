<?php

namespace Drupal\vidyalayawiki\Plugin\Action;

use Drupal\Core\Action\ActionBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\node\NodeInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Path\CurrentPathStack;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\views_bulk_operations\Action\ViewsBulkOperationsPreconfigurationInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Action description.
 *
 * @Action(
 *   id = "vidyalayawiki_set_status_to_paid_action",
 *   label = @Translation("Mark as Paid"),
 *   type = "node"
 * )
 */
class SetStatusToPaidAction extends ActionBase implements ContainerFactoryPluginInterface {

  use StringTranslationTrait;
  protected $currentPath;
  protected $entityTypeManager;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, CurrentPathStack $current_path, EntityTypeManagerInterface $entity_type_managers) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->currentPath = $current_path;
    $this->entityTypeManager = $entity_type_managers;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('path.current'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function execute($node = NULL) {
    $node->original = clone $node;
    $node->set('field_payment_status', 1);
    $node->save();
  }
  /**
   * {@inheritdoc}
   */
  public function access($object, AccountInterface $account = NULL, $return_as_object = FALSE) {
    if (in_array('portal_admin', $account->getRoles()) || in_array('administrator', $account->getRoles())) {
      $access = TRUE;
    }
    else {
      $access = FALSE;
    }
    return $access;
  }
}
