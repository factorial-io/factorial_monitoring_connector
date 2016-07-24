<?php


namespace Drupal\factorial_monitoring_connector\Plugin\MonitoringCollector;


use Drupal\factorial_monitoring_connector\MonitoringCollectorPluginBase;
use Drupal\factorial_monitoring_connector\MonitoringCollectorPluginInterface;
/**
 * Git version collector.
 *
 * @MonitoringCollector(
 *   id = "gitversion",
 *   label = @Translation("Git version monitoring collector plugin.")
 * )
 */
class GitVersionCollector extends MonitoringCollectorPluginBase implements MonitoringCollectorPluginInterface {

  /**
   * Collects data.
   *
   * @return array().
   *   Collected data.
   */
  public function collect() {
    $version = $branch = FALSE;
    $cache_id = 'factorial-monitor-connector-git';
    $timestamp = NULL;
    if ($data = \Drupal::cache()->get($cache_id)) {
      if ($data->expire >= time()) {
        $version = $data->data['version'];
        $branch = $data->data['branch'];
        $timestamp = $data->created;
      }
    }
    if (!$version) {
      exec('git describe --always', $version);
      exec('git rev-parse --abbrev-ref HEAD', $branch);
      $version = trim(implode('', $version));
      $branch = trim(implode('', $branch));
      \Drupal::cache()->set($cache_id, array(
        'version' => $version,
        'branch' => $branch,
      ), time() + 24 * 60 * 60);
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
}
