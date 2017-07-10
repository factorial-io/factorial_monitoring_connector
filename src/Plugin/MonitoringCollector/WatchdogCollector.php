<?php

namespace Drupal\factorial_monitoring_connector\Plugin\MonitoringCollector;

use Drupal\Component\Utility\Html;
use Drupal\Core\Database\Database;
use Drupal\Core\Logger\RfcLogLevel;
use Drupal\factorial_monitoring_connector\MonitoringCollectorPluginBase;
use Drupal\factorial_monitoring_connector\MonitoringCollectorPluginInterface;

/**
 * User collector.
 *
 * @MonitoringCollector(
 *   id = "watchdog",
 *   label = @Translation("Watchdog monitoring collector plugin.")
 * )
 */
class WatchdogCollector extends MonitoringCollectorPluginBase implements MonitoringCollectorPluginInterface {

  /**
   * Collects data.
   *
   * @return array
   *   Collected data.
   */
  public function collect() {
    $last_timestamp = \Drupal::time()->getRequestTime() - 900;

    $connection = Database::getConnection('default');

    $severity_type_mapping = array(
      RfcLogLevel::EMERGENCY => 'error',
      RfcLogLevel::ALERT => 'error',
      RfcLogLevel::CRITICAL => 'error',
      RfcLogLevel::ERROR => 'error',
      RfcLogLevel::WARNING => 'warning',
      RfcLogLevel::NOTICE => 'status',
      RfcLogLevel::INFO => 'status',
      RfcLogLevel::DEBUG => 'status',
    );
    $severity_key_mapping = array(
      RfcLogLevel::EMERGENCY => 'emergency',
      RfcLogLevel::ALERT => 'alert',
      RfcLogLevel::CRITICAL => 'critical',
      RfcLogLevel::ERROR => 'error',
      RfcLogLevel::WARNING => 'warning',
      RfcLogLevel::NOTICE => 'notice',
      RfcLogLevel::INFO => 'info',
      RfcLogLevel::DEBUG => 'debug',
    );
    $count = $connection
      ->query('select count(severity) as count, severity from {watchdog} where timestamp >= :ts group by severity', array(':ts' => $last_timestamp));
    $levels = RfcLogLevel::getLevels();
    $result = array();
    $temp = array();
    $culmulated = array('error' => 0, 'warning' => 0, 'status' => 0);
    foreach ($count as $row) {
      $temp[$row->severity] = $row->count;
      $culmulated[$severity_type_mapping[$row->severity]] += $row->count;
    }
    foreach ($levels as $severity => $desc) {
      $result[] = array(
        'key' => 'severity-count-' . $severity_key_mapping[$severity],
        'group' => 'watchdog',
        'name' => 'Num entries of severity \'' . $desc . '\'',
        'value_type' => 'integer',
        'value' => isset($temp[$severity]) ? $temp[$severity] : 0,
        'type' => isset($temp[$severity]) ? $severity_type_mapping[$severity] : 'status',
      );
    }
    foreach ($culmulated as $key => $count) {
      $result[] = array(
        'key' => 'severity-culmulated-count-' . $key,
        'group' => 'watchdog-culmulated',
        'name' => 'Num culmulated entries of severity \'' . $key . '\'',
        'value_type' => 'integer',
        'value' => $count,
        'type' => 'status',
      );
    }
    $count = $connection->query('select count(severity) as count, type, severity from watchdog where timestamp >= :ts group by type, severity', array(':ts' => $last_timestamp));
    foreach ($count as $row) {
      $result[] = array(
        'key' => 'severity-count-' . Html::cleanCssIdentifier($row->type) . '-' . $severity_key_mapping[$row->severity],
        'group' => 'watchdog-detailed',
        'name' => 'Num entries of severity \'' . $severity_key_mapping[$row->severity] . '\' and type \'' . $row->type . '\'',
        'value_type' => 'integer',
        'value' => $row->count,
        'type' => 'status',
      );
    }
    return $result;
  }

}
