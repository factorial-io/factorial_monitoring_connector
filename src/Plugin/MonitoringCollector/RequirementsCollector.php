<?php

namespace Drupal\factorial_monitoring_connector\Plugin\MonitoringCollector;

use Drupal\Core\Render\Markup;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\factorial_monitoring_connector\MonitoringCollectorPluginBase;
use Drupal\factorial_monitoring_connector\MonitoringCollectorPluginInterface;
use \Drupal\Component\Utility\Html;

/**
 * Requirements version collector.
 *
 * @MonitoringCollector(
 *   id = "requirements",
 *   label = @Translation("Requirements version monitoring collector plugin.")
 * )
 */
class RequirementsCollector extends MonitoringCollectorPluginBase implements MonitoringCollectorPluginInterface {

  /**
   * Collects data.
   *
   * @return array
   *   Collected data.
   */
  public function collect() {

    $requirements = \Drupal::service('system.manager')->listRequirements();

    $mapping = array(
      REQUIREMENT_WARNING => 'warning',
      REQUIREMENT_INFO => 'status',
      REQUIREMENT_ERROR => 'error',
      REQUIREMENT_OK => 'status',
    );

    $return = array();
    foreach ($requirements as $ndx => $requirement) {
      if (isset($requirement['severity']) && ($requirement['severity'] >= REQUIREMENT_WARNING)) {
        $key = 'requirements-' . Html::cleanCssIdentifier(strip_tags($requirement['title']));
        $description = NULL;
        // The description might be a translatable markup, an ordinary string or
        // a render array. TODO: Use more robust code.
        if (isset($requirement['description'])) {
          $r = $requirement['description'];
          if (is_object($r) && ($r instanceof TranslatableMarkup)) {
            $description = $r->render();
          }
          else if ((is_object($r)) && ($r instanceof Markup)) {
            $description = (string) $r;
          }
          else {
            $description = drupal_render($r);
          }
        }
        $return[] = array(
          'key' => $key,
          'group' => 'requirements',
          'type' => $mapping[$requirement['severity']],
          'name' => strip_tags($requirement['title']),
          'value_type' => 'string',
          'value' => strip_tags($requirement['value']),
          'description' => isset($description) ? trim(strip_tags($description)) : NULL,
        );
      }
    }

    return $return;
  }

}
