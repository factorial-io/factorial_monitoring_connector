<?php

namespace Drupal\factorial_monitoring_connector\Plugin\MonitoringCollector;

use Drupal\factorial_monitoring_connector\MonitoringCollectorPluginBase;
use Drupal\factorial_monitoring_connector\MonitoringCollectorPluginInterface;
use Drupal\node\Entity\NodeType;
use Drupal\Core\Database\Database;

/**
 * Node count collector.
 *
 * @MonitoringCollector(
 *   id = "nodecount",
 *   label = @Translation("Node count monitoring collector plugin.")
 * )
 */
class NodeCountCollector extends MonitoringCollectorPluginBase implements MonitoringCollectorPluginInterface
{
    /**
     * Collects data.
     *
     * @return array
     *   Collected data.
     */
    public function collect() {
        $types = NodeType::loadMultiple();
        $return = array();

        foreach($types as $type) {
            $connection = Database::getConnection('default');
            $result = $connection->query('SELECT COUNT(nid) FROM {node} WHERE type=:type', array(
                'type' => $type->id(),
            ))->fetchfield();;
            $return[] = array(
                'key' => $type->id(),
                'name' => 'Number of nodes of type "' . $type->label() . '"',
                'group' => 'node',
                'type' => 'status',
                'value_type' => 'integer',
                'value' => $result,
            );
        }
        return $return;
    }
}