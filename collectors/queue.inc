
<?php

/**
 * @file
 * Collect queue info.
 */

/**
 * Collect queue info.
 */
function factorial_monitoring_connector_collect_queue() {

  $result = db_query('select distinct(name), count(name) as count from {queue}');

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


