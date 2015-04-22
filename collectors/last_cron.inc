<?php

/**
 * @file
 * Collect last cron.
 */

/**
 * Collect last cron.
 */
function factorial_monitoring_connector_collect_last_cron() {
  $last_cron = variable_get('cron_last', 0);
  $delta = time() - $last_cron;
  $type = ($delta > 60 * 60 * 24) ? 'warning' : 'status';

  return array(
    'group' => 'core',
    'type' => $type,
    'name' => 'Last cron run',
    'value_type' => 'timestamp',
    'value' => $last_cron,
  );
}
