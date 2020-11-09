<?php

namespace Drupal\factorial_monitoring_connector\Plugin\MonitoringCollector;

use Drupal\factorial_monitoring_connector\MonitoringCollectorPluginBase;
use Drupal\factorial_monitoring_connector\MonitoringCollectorPluginInterface;
use Drupal\update\UpdateManagerInterface;

/**
 * Update status collector.
 *
 * @MonitoringCollector(
 *   id = "updatestatus",
 *   label = @Translation("Update status monitoring collector plugin.")
 * )
 */
class UpdateStatusCollector extends MonitoringCollectorPluginBase implements MonitoringCollectorPluginInterface
{
    /**
     * Collects data.
     *
     * @return array
     *   Collected data.
     */
    public function collect()
    {
        // Not all hosting providers allow the update-module, so exit early, if
        // necessary.
        if (!\Drupal::moduleHandler()->moduleExists('update')) {
            return;
        }

        if ($available = update_get_available(FALSE)) {
            $return = array();

            module_load_include('inc', 'update', 'update.compare');//??
            $data = update_calculate_project_data($available);
            $mapping = array(
                UpdateManagerInterface::NOT_SECURE => array(
                    'value' => 'Security update available!',
                    'type' => 'error'
                ),
                UpdateManagerInterface::REVOKED => array(
                    'value' => 'Update revoked',
                    'type' => 'error',
                ),
                UpdateManagerInterface::NOT_SUPPORTED => array(
                    'value' => 'Update not supported',
                    'type' => 'warning',
                ),
                UpdateManagerInterface::NOT_CURRENT => array(
                    'value' => 'Updates are available',
                    'type' => 'status',
                ),
                UpdateManagerInterface::CURRENT => array(
                    'value' => 'Up to date',
                    'type' => 'status',
                ),
            );

            if (isset($mapping[$data['drupal']['status']])) {
                $result = $mapping[$data['drupal']['status']];
                $result['key'] = 'update-status-drupal';
                $result['group'] = 'core';
                $result['name'] = 'Update status';
                $return[] = $result;
            }

            unset($data['drupal']);

            foreach ($mapping as $status => $result) {
                foreach ($data as $item) {
                    if ($item['status'] == $status) {
                        if (isset($return[$status])) {
                            $result = $return[$status];
                        }

                        $result['key'] = 'update-status-module-' . $item['name'];
                        $result['group'] = 'contrib';
                        $result['name'] = 'Update status';

                        if (isset($result['description'])) {
                            $result['description'] .= ', ' . $item['name'];
                        } else {
                            $result['description'] = $item['name'];
                        }
                        $return[$status] = $result;
                    }
                }
            }

            return array_values($return);
        }
    }
}