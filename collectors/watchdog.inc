<?php

/**
 * @file
 * Collect watchdog.
 */

/**
 * Collect watchdog.
 */
function factorial_monitoring_connector_collect_watchdog() {
  $last_timestamp = time() - (48 * 60 * 60);

  $severity_type_mapping = array(
    WATCHDOG_EMERGENCY => 'error',
    WATCHDOG_ALERT => 'error',
    WATCHDOG_CRITICAL => 'error',
    WATCHDOG_ERROR => 'error',
    WATCHDOG_WARNING => 'warning',
    WATCHDOG_NOTICE => 'status',
    WATCHDOG_INFO => 'status',
    WATCHDOG_DEBUG => 'status'
  );
  $count = db_query('select count(severity) as count, severity from watchdog where timestamp >= :ts group by severity', array(':ts' => $last_timestamp));
  $levels = watchdog_severity_levels();
  $result = array();
  $temp = array();
  foreach ($count as $row) {
    $temp[$row->severity] = $row->count;
  }
  foreach($levels as $severity => $desc) {
    $result[] = array(
      'key' => 'severity-count-' . $severity,
      'group' => 'watchdog',
      'name' => 'Num entries of severity \''. $desc . '\'',
      'value_type' => 'integer',
      'value' => isset($temp[$severity]) ? $temp[$severity] : 0,
      'type' => isset($temp[$severity]) ? $severity_type_mapping[$severity] : 'status'
    );
  }

  $query = db_query('select * from watchdog where timestamp >= :ts and severity in (:severities) order by timestamp desc', array(
    ':ts' => $last_timestamp,
    ':severities' => array(WATCHDOG_ALERT, WATCHDOG_ERROR, WATCHDOG_CRITICAL, WATCHDOG_EMERGENCY)
  ));

  foreach($query as $row) {
    $result[] = array(
      'key' => 'watchdog-error-' . $row->wid,
      'group' => 'watchdog-errors',
      'name' => 'A critical watchdog-entry was found',
      'description' => strip_tags(t($row->message,unserialize($row->variables))),
      'value' => $row->timestamp,
      'type' => 'error',
      'value_type' => 'timestamp',
    );
  }

  return $result;

}


