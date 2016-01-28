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

  $severity_key_mapping = array(
    WATCHDOG_EMERGENCY => 'emergency',
    WATCHDOG_ALERT => 'alert',
    WATCHDOG_CRITICAL => 'critical',
    WATCHDOG_ERROR => 'error',
    WATCHDOG_WARNING => 'warning',
    WATCHDOG_NOTICE => 'notice',
    WATCHDOG_INFO => 'info',
    WATCHDOG_DEBUG => 'debug'
  );

  $count = db_query('select count(severity) as count, severity from watchdog where timestamp >= :ts group by severity', array(':ts' => $last_timestamp));
  $levels = watchdog_severity_levels();
  $result = array();
  $temp = array();
  $culmulated = array('error' => 0, 'warning' => 0, 'status' => 0);
  foreach ($count as $row) {
    $temp[$row->severity] = $row->count;
    $culmulated[$severity_type_mapping[$row->severity]] += $row->count;
  }
  foreach($levels as $severity => $desc) {
    $result[] = array(
      'key' => 'severity-count-' . $severity_key_mapping[$severity],
      'group' => 'watchdog',
      'name' => 'Num entries of severity \''. $desc . '\'',
      'value_type' => 'integer',
      'value' => isset($temp[$severity]) ? $temp[$severity] : 0,
      'type' => isset($temp[$severity]) ? $severity_type_mapping[$severity] : 'status'
    );
  }

  foreach($culmulated as $key => $count) {
    $result[] = array(
      'key' => 'severity-culmulated-count-' . $key,
      'group' => 'watchdog culmulated',
      'name' => 'Num culmulated entries of severity \''. $key . '\'',
      'value_type' => 'integer',
      'value' => $count,
      'type' => 'status'
    );
  }


  $query = db_query('select * from watchdog where timestamp >= :ts and severity in (:severities) order by timestamp desc', array(
    ':ts' => $last_timestamp,
    ':severities' => array(WATCHDOG_ALERT, WATCHDOG_ERROR, WATCHDOG_CRITICAL, WATCHDOG_EMERGENCY)
  ));
  foreach($query as $row) {
    $vars = unserialize($row->variables);
    if (!is_array($vars)) {
      $vars = array();
    }

    $result[] = array(
      'key' => 'watchdog-error-' . $row->wid,
      'group' => 'watchdog-errors',
      'name' => 'A critical watchdog-entry was found',
      'description' => strip_tags(t($row->message, $vars)),
      'value' => $row->timestamp,
      'type' => $severity_type_mapping[$row->severity],
      'value_type' => 'timestamp',
    );
  }

  return $result;

}


