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
 * Link to the URL to create an Article.
 *
 * @Block(
 *   id = "create article",
 *   admin_label = @Translation("Create Articles under a Topic"),
 *   category = @Translation("Custom")
 * )
 */
class CreateArticleBlock extends BlockBase implements ContainerFactoryPluginInterface {

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
    if ($this->routeMatch->getRouteName() == 'entity.node.canonical') {
      $node = $this->routeMatch->getParameter('node');
      if ($node->getType() == 'topic') {
        $uid = $this->account->id();
        $kb_id = $node->field_knowledge_board->target_id;
        $skb_id = $node->field_sub_knowledge_board->target_id;
        $checkAccess = \Drupal::database()->query("SELECT COUNT(*) FROM `user__roles` WHERE roles_target_id IN ('administrator', 'portal_admin', 'project_invigilator') AND entity_id = :uid", [':uid' => $uid])->fetchField();
        if ($checkAccess == 0) {
          $checkAccess = \Drupal::database()->query("SELECT COUNT(entity_id) FROM `node__field_knowledge_board_managers` WHERE field_knowledge_board_managers_target_id = :uid AND entity_id = :nid", [':uid' => $uid, ':nid' => $kb_id])->fetchField();
          if ($checkAccess == 0) {
            $checkAccess = \Drupal::database()->query("SELECT COUNT(entity_id) FROM `node__field_skb_managers` WHERE field_skb_managers_target_id = :uid AND entity_id = :nid", [':uid' => $uid, ':nid' => $skb_id])->fetchField();
            if ($checkAccess == 0) {
              $checkAccess = \Drupal::database()->query("SELECT COUNT(entity_id) FROM `node__field_experts` WHERE field_experts_target_id = :uid AND entity_id = :nid", [':uid' => $uid, ':nid' => $node->id()])->fetchField();
            }
          }
        }
        if ($checkAccess >= 1) {
          $add_link_url = Url::fromRoute("node.add", ['node_type' => 'article'], ['query' => ['topic' => $node->id(), 'skb' => $skb_id, 'kb' => $kb_id]])->toString();
          $block_html = '<a href="' . $add_link_url . '">';
          $block_html .= '<div class="fa fa-plus iconblocks"></div>';
          $block_html .= '<div class="linkblocks">Add an Article</div></a>';
          $build['#markup'] =  $block_html;
        }
      }
      $build['#cache']['max-age'] = 0;
      return $build;
    }
  }

}
