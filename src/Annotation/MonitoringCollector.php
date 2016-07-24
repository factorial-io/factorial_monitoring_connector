<?php

namespace Drupal\factorial_monitoring_connector\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
* Defines a reusable form plugin annotation object.
*
* @Annotation
*/
class MonitoringCollector extends Plugin {

  /**
  * The plugin ID.
  *
  * @var string
  */
  public $id;

  /**
  * The name of the form plugin.
  *
  * @var \Drupal\Core\Annotation\Translation
  *
  * @ingroup plugin_translatable
  */
  public $label;

}
