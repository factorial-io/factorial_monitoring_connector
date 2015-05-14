<?php

/**
 * @file
 * Collect last cron.
 */

/**
 * Collect last cron.
 */
function factorial_monitoring_connector_collect_gitversion() {

  $version = $branch = FALSE;
  $cache_id = 'factorial-monitor-connector-git';
  $timestamp = NULL;

  if ($data = cache_get($cache_id)) {
    if ($data->expire >= time()) {
      $version = $data->data['version'];
      $branch = $data->data['branch'];
      $timestamp = $data->created;
    }
  }
  if (!$version) {
    exec('git describe --always', $version);
    exec('git rev-parse --abbrev-ref HEAD', $branch);
    $version = trim(join('', $version));
    $branch = trim(join('', $branch));

    cache_set($cache_id, array(
      'version' => $version,
      'branch' => $branch,
    ), 'cache', time() + 24 * 60 * 60);
  }
  return array(
    array(
      'key' => 'version',
      'group' => 'git',
      'type' => 'status',
      'name' => 'Current version',
      'value_type' => 'string',
      'value' => $version,
      'ts' => $timestamp,
    ),
    array(
      'key' => 'branch',
      'group' => 'git',
      'type' => 'status',
      'name' => 'Current branch',
      'value_type' => 'string',
      'value' => $branch,
      'ts' => $timestamp,
    ),
  );
}


