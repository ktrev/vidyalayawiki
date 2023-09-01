<?php

namespace Drupal\vidyalayawiki\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\Core\Routing\CurrentRouteMatch;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Link to the URL to make a payment.
 *
 * @Block(
 *   id = "create payment",
 *   admin_label = @Translation("Make Payment"),
 *   category = @Translation("Custom")
 * )
 */
class CreatePaymentBlock extends BlockBase implements ContainerFactoryPluginInterface {

  protected $routeMatch;
  protected $account;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, CurrentRouteMatch $route_match, AccountInterface $account) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->routeMatch = $route_match;
    $this->account = $account;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('current_route_match'),
      $container->get('current_user')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    $userViewedUid = $this->routeMatch->getParameter('user');
    $loggedInUeruid = $this->account->id();
    $checkAccess = \Drupal::database()->query("SELECT COUNT(*) FROM `user__roles` WHERE roles_target_id IN ('administrator', 'portal_admin', 'project_invigilator') AND entity_id = :uid", [':uid' => $loggedInUeruid])->fetchField();
    if ($checkAccess >= 1) {
      $add_link_url = Url::fromRoute("node.add", ['node_type' => 'payment_details'], ['query' => ['uid' => $userViewedUid]])->toString();
      $block_html = '<a href="' . $add_link_url . '">';
      $block_html .= '<div class="fa fa-rupee iconblocks"></div>';
      $block_html .= '<div class="linkblocks">Make Payment</div></a>';
      $build['#markup'] =  $block_html;
    }
    $build['#cache']['max-age'] = 0;
    return $build;
  }

}
