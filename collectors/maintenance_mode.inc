<?php
/**
 * @file
 * Collect maintenance_mode.
 */

/**
 * Collect maintenance_mode.
 */
function factorial_monitoring_connector_collect_maintenance_mode() {
  $maintenance_mode = variable_get('maintenance_mode', FALSE);
  $msg = $maintenance_mode ? variable_get('maintenance_mode_message', NULL) : NULL;
  return array(
    'group' => 'core',
    'type' => $maintenance_mode ? 'error' : 'status',
    'name' => 'Maintenance mode',
    'description' => $msg,
    'value_type' => 'boolean',
    'value' => $maintenance_mode,
  );
}
