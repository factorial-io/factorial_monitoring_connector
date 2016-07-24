<?php

namespace Drupal\factorial_monitoring_connector;


use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;

interface MonitoringCollectorPluginInterface extends PluginInspectionInterface, ContainerFactoryPluginInterface {

  /**
   * Return the name of the reusable form plugin.
   *
   * @return string
   */
  public function getName();

  /**
   * Collects data.
   *
   * @return array().
   *   Collected data.
   */
  public function collect();

}
