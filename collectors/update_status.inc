<?php
/**
 * @file
 * Collect update status.
 */

/**
 * Collect update status.
 */
function factorial_monitoring_connector_collect_update_status() {

  if ($available = update_get_available(FALSE)) {
    $return = array();

    module_load_include('inc', 'update', 'update.compare');
    $data = update_calculate_project_data($available);
    $mapping = array(
      UPDATE_NOT_SECURE => array(
        'value' => 'Security update available!',
        'type' => 'error'
      ),
      UPDATE_REVOKED => array(
        'value' => 'Update revoked',
        'type' => 'error',
      ),
      UPDATE_NOT_SUPPORTED => array(
        'value' => 'Update not supported',
        'type' => 'warning',
      ),
      UPDATE_NOT_CURRENT => array(
        'value' => 'Updates are available',
        'type' => 'status',
      ),
      UPDATE_CURRENT => array(
        'value' => 'Up to date',
        'type' => 'status',
      ),
    );

    if (isset($mapping[$data['drupal']['status']])) {
      $result = $mapping[$data['drupal']['status']];
      $result['group'] = 'core';
      $result['name'] = 'Update status';
      $return[] = $result;
    }

    unset($data['drupal']);

    foreach ($mapping as $status => $result) {
      foreach ($data as $item) {
        if ($item['status'] == $status) {
          if (isset($return[$status])) {
            $result = $return[$status];
          }

          $result['group'] = 'contrib';
          $result['name'] = 'Update status';

          if (isset($result['description'])) {
            $result['description'] .= ', ' . $item['name'];
          }
          else {
            $result['description'] = $item['name'];
          }
          $return[$status] = $result;
        }
      }
    }

    return array_values($return);
  }
}
