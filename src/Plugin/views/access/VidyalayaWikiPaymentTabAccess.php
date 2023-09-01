<?php
/**
 * These declarations are REQUIRED in this comment...
 *
 * @ingroup views_access_plugins
 *
 * @ViewsAccess(
 *   id = "vidyalayawiki_payment.access_handler",
 *   title = @Translation("Vidyalayawiki Payment Tab Access"),
  * )
 */

namespace Drupal\vidyalayawiki\Plugin\views\access;

use Drupal\views\Plugin\views\access\AccessPluginBase;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\Routing\Route;

class VidyalayaWikiPaymentTabAccess extends AccessPluginBase {

  public function summaryTitle() {
    return $this->t('Vidyalayawiki Payment Tab Access');
  }
  
  public function access(AccountInterface $account) {
    // If we have made it here, I am OK with proceeding.
    return TRUE;
  }
  
  
  /**
   * We want to ask a custom function, which will examine the route (and the node we are looking at) to
   * decide if this should proceed.
   * @param Route $route
   */
  public function alterRouteDefinition(Route $route) {    
    $route->setRequirement('_custom_access', '\Drupal\vidyalayawiki\Controller\VidyalayaWikiController:isAllowedAccessToEventOutcomes');
    
  } 
  
  /**
   * Do not cache.
   * @return number
   */
  public function getCacheMaxAge() {
    return 0;
  }
  
} // class