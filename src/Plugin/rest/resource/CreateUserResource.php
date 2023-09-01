<?php

namespace Drupal\vidyalayawiki\Plugin\rest\resource;

use Drupal\rest\ModifiedResourceResponse;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\user\Entity\User;

/**
 * Provides a resource to create user.
 *
 * @RestResource(
 *   id = "app_createuser_resource",
 *   label = @Translation("API to Create User"),
 *   uri_paths = {"canonical" = "/api/createuser", "create" = "/api/createuser"
 *   }
 * )
 */
class CreateUserResource extends ResourceBase {

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
      $checkUser = db_query("SELECT COUNT(*) FROM `users_field_data` WHERE mail = :mail", [':mail' => $data['mail']])->fetchField();
      if ($checkUser == 0) {
        $user = User::create([]);
        $user->setUsername($data['mail']);
        $user->enforceIsNew();
        if (isset($data['mail'])) {
          $user->set("init", $data['mail']);
        }
        $user->set("created", time());
        if (isset($data['fullname'])) {
          $user->set('field_full_name', $data['fullname']);
        }
        $user->activate();
        $user->save();
        $uid = $user->id();
      }
    }
    $message = ['success' => $uid, 'message' => 'user created'];
    return new ModifiedResourceResponse($message, 200);
  }

}
