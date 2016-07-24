<?php

namespace Drupal\factorial_monitoring_connector;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;

class MonitoringCollectorManager extends DefaultPluginManager {
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct('Plugin/MonitoringCollector', $namespaces, $module_handler, 'Drupal\factorial_monitoring_connector\MonitoringCollectorPluginInterface', 'Drupal\factorial_monitoring_connector\Annotation\MonitoringCollector');
    $this->alterInfo('monitoring_collector_info');
    $this->setCacheBackend($cache_backend, 'factorial_monitoring_collectors');
  }

}
