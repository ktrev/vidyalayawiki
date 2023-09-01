<?php

function vidyalayawiki_post_update_add_my_action() {
  \Drupal::entityTypeManager()->getStorage('action')->create([
    'id' => 'vidyalayawiki_set_status_to_paid_action',
    'label' => 'Mark as Paid',
    'type' => 'node',
    'plugin' => 'vidyalayawiki_set_status_to_paid_action',
  ])->save();
}