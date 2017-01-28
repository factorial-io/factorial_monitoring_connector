<?php

namespace Drupal\factorial_monitoring_connector\Plugin\MonitoringCollector;

use Drupal\factorial_monitoring_connector\MonitoringCollectorPluginBase;
use Drupal\factorial_monitoring_connector\MonitoringCollectorPluginInterface;

/**
 * Core version collector.
 *
 * @MonitoringCollector(
 *   id = "coreversion",
 *   label = @Translation("Core version monitoring collector plugin.")
 * )
 */
class CoreVersionCollector extends MonitoringCollectorPluginBase implements MonitoringCollectorPluginInterface {

  /**
   * Collects data.
   *
   * @return array
   *   Collected data.
   */
  public function collect() {
    return array(
      array(
        'group' => 'core',
        'type' => 'status',
        'name' => 'Drupal version',
        'value_type' => 'string',
        'value' => \Drupal::VERSION,
      ),
    );
  }

}
