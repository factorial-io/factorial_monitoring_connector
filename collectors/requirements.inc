<?php

/**
 * @file
 * Collect requirements.
 */

/**
 * Collect requirements.
 */
function factorial_monitoring_connector_collect_requirements() {

  include_once DRUPAL_ROOT . '/includes/install.inc';
  drupal_load_updates();

  // Check run-time requirements and status information.
  $requirements = module_invoke_all('requirements', 'runtime');
  usort($requirements, '_system_sort_requirements');

  $return = array();
  foreach ($requirements as $ndx => $requirement) {
    if (isset($requirement['severity']) && ($requirement['severity'] >= REQUIREMENT_WARNING)) {
      $return[] = array(
        'key' => 'requirement-' . $ndx,
        'group' => 'requirements',
        'type' => $requirement['severity'] == REQUIREMENT_WARNING ? 'warning' : 'error',
        'name' => strip_tags($requirement['title']),
        'value_type' => 'string',
        'value' => strip_tags($requirement['value']),
        'description' => isset($requirement['description']) ? strip_tags($requirement['description']) : NULL,
      );
    }
  }

  return $return;
}


