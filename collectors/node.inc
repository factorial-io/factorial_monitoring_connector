
<?php

/**
 * @file
 * Collect node info.
 */

/**
 * Collect node info.
 */
function factorial_monitoring_connector_collect_node() {

  $types = node_type_get_types();
  $return = array();

  foreach($types as $type) {
    $result = db_query('SELECT COUNT(nid) FROM {node} WHERE type=:type', array(
      'type' => $type->type,
    ))->fetchfield();
    $return[] = array(
      'key' => $type->type,
      'name' => 'Number of nodes of type "' . $type->name . '"',
      'group' => 'node',
      'type' => 'status',
      'value_type' => 'integer',
      'value' => $result,
    );
  }
  return $return;
}


