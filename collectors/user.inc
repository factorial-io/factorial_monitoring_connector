<?php

/**
 * @file
 * Collect last cron.
 */

/**
 * Collect last cron.
 */
function factorial_monitoring_connector_collect_user() {

  $interval = REQUEST_TIME - 900;


  $flood_count = db_query("SELECT COUNT(fid) FROM {flood} f WHERE f.timestamp > :timestamp", array(":timestamp" => $interval))->fetchField();
  $user_count = db_query("SELECT COUNT(uid) FROM {users}")->fetchField();
  $blocked_count = db_query("SELECT COUNT(uid) FROM {users} WHERE status='0' AND uid != 0")->fetchField();
  $authenticated_count = db_query("SELECT COUNT(DISTINCT s.uid) FROM {sessions} s WHERE s.timestamp >= :timestamp AND s.uid > 0", array(':timestamp' => $interval))->fetchField();

  return array(
    array(
      'key' => 'count',
      'group' => 'user',
      'type' => 'status',
      'name' => 'Amount of users',
      'value_type' => 'integer',
      'value' => $user_count,
    ),
    array(
      'key' => 'logged-in',
      'group' => 'user',
      'type' => 'status',
      'name' => 'Logged in users',
      'value_type' => 'integer',
      'value' => $authenticated_count,
    ),
    array(
      'key' => 'blocked',
      'group' => 'user',
      'type' => 'status',
      'name' => 'Blocked users',
      'value_type' => 'integer',
      'value' => $blocked_count,
    ),
    array(
      'key' => 'login-failures',
      'group' => 'user',
      'type' => 'status',
      'name' => 'Login failures',
      'value_type' => 'integer',
      'value' => $flood_count,
    )
  );
}


