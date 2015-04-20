<?php
function factorial_monitoring_connector_collect_core_version() {
  return array(
    'group' => 'core',
    'type' => 'status',
    'name' => 'Drupal version',
    'value_type' => 'string',
    'value' => VERSION
  );
}
