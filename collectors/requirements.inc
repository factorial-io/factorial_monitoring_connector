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

  $mapping = array(
    REQUIREMENT_WARNING => 'warning',
    REQUIREMENT_INFO => 'status',
    REQUIREMENT_ERROR => 'error',
    REQUIREMENT_OK => 'status',
  );

  $return = array();
  foreach ($requirements as $ndx => $requirement) {
    if (isset($requirement['severity']) && ($requirement['severity'] >= REQUIREMENT_WARNING)) {
      $key = 'requirements-' . drupal_html_class(strip_tags($requirement['title']));
      $return[] = array(
        'key' => $key,
        'group' => 'requirements',
        'type' => $mapping[$requirement['severity']],
        'name' => strip_tags($requirement['title']),
        'value_type' => 'string',
        'value' => strip_tags($requirement['value']),
        'description' => isset($requirement['description']) ? strip_tags($requirement['description']) : NULL,
      );
    }
  }

  return $return;
}
