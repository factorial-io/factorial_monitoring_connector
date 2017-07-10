<?php

namespace Drupal\factorial_monitoring_connector\Plugin\MonitoringCollector;

use Drupal\Core\Database\Database;
use Drupal\factorial_monitoring_connector\MonitoringCollectorPluginBase;
use Drupal\factorial_monitoring_connector\MonitoringCollectorPluginInterface;

/**
 * User collector.
 *
 * @MonitoringCollector(
 *   id = "user",
 *   label = @Translation("User monitoring collector plugin.")
 * )
 */
class UserCollector extends MonitoringCollectorPluginBase implements MonitoringCollectorPluginInterface {

  /**
   * Collects data.
   *
   * @return array
   *   Collected data.
   */
  public function collect() {
    $interval = \Drupal::time()->getRequestTime() - 900;

    $connection = Database::getConnection('default');

    $flood_count = $connection
      ->query("SELECT COUNT(fid) FROM {flood} f WHERE f.timestamp > :timestamp", array(":timestamp" => $interval))
      ->fetchField();

    $user_count = \Drupal::entityQuery('user')
      ->count()
      ->execute();

    $blocked_count = \Drupal::entityQuery('user')#
      ->condition('status', 0)
      ->count()
      ->execute();

    $authenticated_count = $connection
      ->query("SELECT COUNT(DISTINCT s.uid) FROM {sessions} s WHERE s.timestamp >= :timestamp AND s.uid > 0", array(':timestamp' => $interval))
      ->fetchField();


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

}
