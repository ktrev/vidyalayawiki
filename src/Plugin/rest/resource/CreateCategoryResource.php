<?php

namespace Drupal\vidyalayawiki\Plugin\rest\resource;

use Drupal\rest\ModifiedResourceResponse;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\taxonomy\Entity\Term;

/**
 * Provides a resource to create a term in the taxonomy Category.
 *
 * @RestResource(
 *   id = "app_createcategory_resource",
 *   label = @Translation("API to Create Category"),
 *   uri_paths = {"canonical" = "/api/createcategory", "create" = "/api/createcategory"
 *   }
 * )
 */
class CreateCategoryResource extends ResourceBase {

  /**
   * Responds to POST requests.
   */
  public function post(array $data = []) {
    $access_token = $_SERVER['HTTP_TOKEN'];
    if ($access_token != '414427090b834290bcee1dd204fdb0f9') {
      $message = ['success' => 0, 'message' => 'Authentication Failed'];
      return new ModifiedResourceResponse($message, 200);
    }
    else {
      $checkTerm = db_query("SELECT COUNT(*) FROM `taxonomy_term__field_indicwiki_id` WHERE field_indicwiki_id_value = :cat_id", [':cat_id' => $data['cat_id']])->fetchField();
      if ($checkTerm == 0) {
        $term = Term::create([
          'vid' => 'category',
          'name' => $data['cat_title'],
          'parent' => [0],
          'field_indicwiki_id' => $data['cat_id'],
        ]);
        $term->save();
      }
    }
    $message = ['success' => $term->id(), 'message' => 'category term created'];
    return new ModifiedResourceResponse($message, 200);
  }

}
