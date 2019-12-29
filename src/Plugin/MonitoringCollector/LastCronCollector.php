<?php

namespace Drupal\factorial_monitoring_connector\Plugin\MonitoringCollector;

use Drupal\factorial_monitoring_connector\MonitoringCollectorPluginBase;
use Drupal\factorial_monitoring_connector\MonitoringCollectorPluginInterface;

/**
 * Last cron collector.
 *
 * @MonitoringCollector(
 *   id = "lastcron",
 *   label = @Translation("Last cron monitoring collector plugin.")
 * )
 */
class LastCronCollector extends MonitoringCollectorPluginBase implements MonitoringCollectorPluginInterface
{
    /**
     * Collects data.
     *
     * @return array
     *   Collected data.
     */
    public function collect() {
        $last_cron = \Drupal::state()->get('system.cron_last',0);
        $delta = \Drupal::time()->getCurrentTime() - $last_cron;
        $type = ($delta > 60 * 60 * 24) ? 'warning' : 'status';

        return array(
            array(
            'group' => 'core',
            'type' => $type,
            'name' => 'Last cron run',
            'value_type' => 'timestamp',
            'value' => $last_cron,
            )
        );
    }

}