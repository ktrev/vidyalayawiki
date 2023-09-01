<?php

namespace Drupal\vidyalayawiki\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
  * Implements Class VidyalayaWikiController.
  */
class VidyalayaWikiController extends ControllerBase {
  
  protected $account;
  
  /**
   * Class constructor.
   */
  public function __construct(AccountInterface $account) {
    $this->account = $account;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    // Instantiates this form class.
    return new static(
      // Load the service required to construct this class.
      $container->get('current_user')
    );
  }


  /**
   * Constructs an end point to manage indic wiki.
   *
   * The router _controller callback, maps the path
   * '/managee' to this method.
   */
  public function manage() {
    $currentUsersUid = $this->account->id();
    if (in_array('project_invigilator', $this->account->getRoles()) || in_array('portal_admin', $this->account->getRoles()) || in_array('administrator', $this->account->getRoles())) {
      $role = 'Admin';
    }
    elseif (in_array('knowledge_manager', $this->account->getRoles())) {
      $role = 'KB';
    }
    elseif (in_array('sub_knowledge_manager', $this->account->getRoles())) {
      $role = 'SKB';
    }
    elseif (in_array('expert', $this->account->getRoles())) {
      $role = 'Expert';
    }
    elseif (in_array('writer', $this->account->getRoles())) {
      $role = 'Writer';
    }
    elseif (in_array('language_expert', $this->account->getRoles())) {
      $role = 'Language Expert';
    }
    elseif (in_array('markup_manager', $this->account->getRoles())) {
      $role = 'Markup Manager';
    }
    elseif (in_array('format_checker', $this->account->getRoles())) {
      $role = 'Format Checker';
    }
    elseif (in_array('finance_manager', $this->account->getRoles())) {
      $role = 'Finance Manager';
    }
    else {
      $role = 'Normal';
    }

    $render = [
      '#theme' => 'vidyalayawiki-manage-template',
      '#role' => $role,
    ];
    return $render;
  }
  
  public function isAllowedAccessToEventOutcomes() {
    
    $bundle = FALSE;
    $route = \Drupal::routeMatch();
    $routeName = $route->getRouteName();
    $node = $route->getParameter('node');
    if ($node instanceof \Drupal\node\NodeInterface) {
      $bundle = $node->bundle();
    }
    if (in_array('finance_manager', $this->account->getRoles()) || in_array('project_invigilator', $this->account->getRoles()) || in_array('portal_admin', $this->account->getRoles()) || in_array('administrator', $this->account->getRoles())) {
      $role = 'admin';
    }
    else {
      $role = 'authenticated';
    }
    // the first part ensures the local task (tab) is shown on node pages of type 'event'
    // the second part makes sure the tab also displays on the view itself  
    //      (the view route is view.VIEW_MACHINE_NAME.DISPLAY_MACHINE_NAME)
    return AccessResult::allowedIf($role == 'admin' && ($bundle === 'article' || $routeName === 'view.payment_status.page_2'));
  }
  
  
  
  /**
   * Do not cache
   * @return number
   */
  public function getCacheMaxAge() {
    return 0;
  }
}
