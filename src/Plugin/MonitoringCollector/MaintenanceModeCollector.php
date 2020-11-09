<?php

namespace Drupal\factorial_monitoring_connector\Plugin\MonitoringCollector;

use Drupal\factorial_monitoring_connector\MonitoringCollectorPluginBase;
use Drupal\factorial_monitoring_connector\MonitoringCollectorPluginInterface;

/**
 * Maintenance mode collector.
 *
 * @MonitoringCollector(
 *   id = "maintenancemode",
 *   label = @Translation("Maintenance mode monitoring collector plugin.")
 * )
 */
class MaintenanceModeCollector extends MonitoringCollectorPluginBase implements MonitoringCollectorPluginInterface
{
    /**
     * Collects data.
     *
     * @return array
     *   Collected data.
     */
    public function collect() {
        $maintenance_mode = \Drupal::state()->get('system.maintenance_mode',FALSE);
        $msg = $maintenance_mode ? \Drupal::state()->get('system.maintenance_mode_message',NULL) : NULL;
        return array(
            array(
            'key' => 'maintenance-mode',
            'group' => 'core',
            'type' => $maintenance_mode ? 'error' : 'status',
            'name' => 'Maintenance mode',
            'description' => $msg,
            'value_type' => 'boolean',
            'value' => $maintenance_mode,
            )
        );
    }
}