<?php

namespace Drupal\vidyalayawiki\Plugin\rest\resource;

use Drupal\rest\ModifiedResourceResponse;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\user\Entity\User;
use Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Term;


/**
 * Provides a resource to create an article/page.
 *
 * @RestResource(
 *   id = "app_createarticle_resource",
 *   label = @Translation("API to Create Article"),
 *   uri_paths = {"canonical" = "/api/createarticle", "create" = "/api/createarticle"
 *   }
 * )
 */
class CreateArticleResource extends ResourceBase {

  /**
   * Responds to POST requests.
   */
  public function post(array $data = []) {
  // \Drupal::logger('indicwiki_debug')->warning('<pre><code>' . print_r($data, TRUE) . '</code></pre>');
    $access_token = $_SERVER['HTTP_TOKEN'];
    if ($access_token != '414427090b834290bcee1dd204fdb0f9') {
      $message = ['success' => 0, 'message' => 'Authentication Failed'];
      return new ModifiedResourceResponse($message, 200);
    }
    else {
      $writer_ids = [];
      $class_array = [];
      $subject_array = [];
      $class_subject = 0;
      $contributors_ids = [];
      $category_ids = [];
      $readyForPayment = 0;
      $checkPage = \Drupal::database()->query("SELECT COUNT(entity_id) FROM `node__field_indicwiki_page_id` WHERE field_indicwiki_page_id_value = :page_id", [':page_id' => $data['page_id']])->fetchField();
      if ($checkPage == 0) {
	//\Drupal::logger('indicwiki_debug_page')->warning('<pre><code>' . print_r($checkPage, TRUE) . '</code></pre>');
        $namespace = $data['namespace'];
        $authors = $data['authors'];
        $categories = $data['categories'];
        for($i = 0; $i < count($authors); $i++) {
          //if (isset($authors[$i]['mail']) && $authors[$i]['mail'] != null && $authors[$i]['mail'] != '') {
            $checkUser = \Drupal::database()->query("SELECT uid FROM `users_field_data` WHERE name = :name", [':name' => $authors[$i]['name']])->fetchField();
            if (isset($checkUser) && $checkUser > 0) {
              $roles = \Drupal::database()->query("SELECT roles_target_id FROM `user__roles` WHERE entity_id = :userid", [':userid' => $checkUser])->fetchAll();
              if (count($roles) == 0) {
                array_push($writer_ids, $checkUser);               
              }
              array_push($contributors_ids, $checkUser);
            }
            else {
              $user = User::create([]);
              $user->setUsername($authors[$i]['name']);
              if (isset($authors[$i]['fullname'])) {
                $user->set('field_full_name', $authors[$i]['fullname']);
              }
              $user->set("init", $authors[$i]['mail']);
              $user->set("mail", $authors[$i]['mail']);
              $user->set("created", time());
              $user->activate();
              $user->save();
              array_push($writer_ids, $user->id());
              array_push($contributors_ids, $user->id());
            }
            
          //}
        }
        for($j = 0; $j < count($categories); $j++) {
          if ($categories[$j]['id'] == 349) {
            $readyForPayment = 1;
          }
          if ($categories[$j]['id'] == 266) {
            array_push($class_array, 119879);
            $class_subject = 1;
          }
          if ($categories[$j]['id'] == 267) {
            array_push($class_array, 119880);
            $class_subject = 1;
          }
          if ($categories[$j]['id'] == 268) {
            array_push($class_array, 119881);
            $class_subject = 1;
          }
          if ($categories[$j]['id'] == 269) {
            array_push($class_array, 119882);
            $class_subject = 1;
          }
          if ($categories[$j]['id'] == 39) {
            array_push($subject_array, 119883);
            $class_subject = 1;
          }
          if ($categories[$j]['id'] == 204) {
            array_push($subject_array, 119884);
            $class_subject = 1;
          }
          if ($categories[$j]['id'] == 73) {
            array_push($subject_array, 119885);
            $class_subject = 1;
          }
          if ($categories[$j]['id'] == 149) {
            array_push($subject_array, 119886);
            $class_subject = 1;
          }
          if ($class_subject == 0) {
          $checkTerm = \Drupal::database()->query("SELECT entity_id FROM `taxonomy_term__field_indicwiki_id` WHERE field_indicwiki_id_value = :cat_id", [':cat_id' => $categories[$j]['id']])->fetchField();
            if (isset($checkTerm) && $checkTerm > 0) {
              array_push($category_ids, $checkTerm);
            }
            else {
              $term = Term::create([
                'vid' => 'category',
                'name' => $categories[$j]['name'],
                'parent' => [0],
                'field_indicwiki_id' => $categories[$j]['id'],
              ]);
              $term->save();
              array_push($category_ids, $term->id());
            }
          }
        }
        if (count($writer_ids) > 0 && $readyForPayment == 1) {
          $namespace_tid = \Drupal::database()->query('SELECT entity_id FROM `taxonomy_term__field_namespace_id` WHERE field_namespace_id_value = :namespace', [':namespace' => $namespace])->fetchField();
          $article_node = Node::create([
            'type' => 'article',
            'title' => $data['title'],
            'langcode' => 'en',
            'uid' => '1',
            'status' => 1,
            'field_indicwiki_page_id' => ['value' => $data['page_id']],
            'field_status' => ['value' => 'Published'],
            'field_payment_status' => ['value' => 0],
            'field_link_to_the_indic_wiki' => [
              'uri' => 'https://www.vidyalayawiki.in/index.php?title=' . $data['title'],
              'title' => 'Go to the Vidyalaya Wiki page'
            ],
            'field_namespace' => ['target_id' => $namespace_tid],
            'field_contributors' => $contributors_ids,
            'field_writer' => $writer_ids,
            'field_category' => $category_ids,
            'field_class' => $class_array,
            'field_subject' => $subject_array,
          ]);

          $article_node->save();
        }
      }
    }
    $message = ['success' => 1, 'message' => 'Artiles created'];
    return new ModifiedResourceResponse($message, 200);
  }

}
