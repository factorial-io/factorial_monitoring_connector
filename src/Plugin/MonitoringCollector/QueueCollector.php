<?php

namespace Drupal\factorial_monitoring_connector\Plugin\MonitoringCollector;

use Drupal\Core\Database\Database;
use Drupal\Core\Logger\RfcLogLevel;
use Drupal\factorial_monitoring_connector\MonitoringCollectorPluginBase;
use Drupal\factorial_monitoring_connector\MonitoringCollectorPluginInterface;

/**
 * Queue collector.
 *
 * @MonitoringCollector(
 *   id = "queue",
 *   label = @Translation("Queue monitoring collector plugin.")
 * )
 */
class QueueCollector extends MonitoringCollectorPluginBase implements MonitoringCollectorPluginInterface {

  /**
   * Collects data.
   *
   * @return array
   *   Collected data.
   */
  public function collect() {
    $connection = Database::getConnection('default');
    $result = $connection->query('SELECT count(name) AS count, name FROM {queue} GROUP BY name');
    $return = array();
    while($row = $result->fetchObject()) {
      $return[] = array(
        'key' => $row->name,
        'name' => 'Elements in queue ' . $row->name,
        'group' => 'queue',
        'type' => 'status',
        'value_type' => 'integer',
        'value' => $row->count,
      );
    }
    return $return;
  }

}
