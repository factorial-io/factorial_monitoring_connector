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

  $count = db_query('select count(severity) as count, severity from {watchdog} where timestamp >= :ts group by severity', array(':ts' => $last_timestamp));
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
      'group' => 'watchdog-culmulated',
      'name' => 'Num culmulated entries of severity \''. $key . '\'',
      'value_type' => 'integer',
      'value' => $count,
      'type' => 'status'
    );
  }

  $count = db_query('select count(severity) as count, type, severity from watchdog where timestamp >= :ts group by type, severity', array(':ts' => $last_timestamp));

  foreach ($count as $row) {
    $result[] = array(
      'key' => 'severity-count-' . drupal_clean_css_identifier($row->type) . '-' . $severity_key_mapping[$row->severity],
      'group' => 'watchdog-detailed',
      'name' => 'Num entries of severity \''. $severity_key_mapping[$row->severity] . '\' and type \'' . $row->type . '\'',
      'value_type' => 'integer',
      'value' => $row->count,
      'type' => $severity_type_mapping[$row->severity]
    );
  }

  return $result;
}


