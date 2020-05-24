<?php

namespace Drupal\factorial_monitoring_connector\Controller;

//require __DIR__ . '/../../vendor/autoload.php';

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Defuse\Crypto\Crypto;
use Defuse\Crypto\Key;
use Defuse\Crypto\Exception as Ex;

/**
 * Class MonitorStateController.
 */
class MonitorStateController extends ControllerBase {

  /**
   * Get the current version.
   *
   * @return string
   *   The version.
   */
  public static function getVersion() {
    return '1.1.0';
  }

  /**
   * Collects the current state.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   Return the results.
   *
   * @throws Ex\CryptoException
   */
  public function collectCurrentState(Request $request) {

    $json = $this->collect();
    $json = $this->encrypt($json);

    return new JsonResponse($json);
  }

  /**
   * Collect all results.
   *
   * @return array
   *   All results.
   */
  public function collect() {
    $results = array();
    $service = \Drupal::service('plugin.manager.factorial_monitoring_connector.collectorplugins');
    foreach ($service->getDefinitions() as $definition) {
      $plugin = $service->createInstance($definition['id']);
      $starttick = microtime(TRUE);
      $results = array_merge($results, $plugin->collect());
      $delta = microtime(TRUE) - $starttick;
      $results[] = array(
        'group' => 'timing',
        'key' => 'monitor-' . $definition['id'],
        'value' => round($delta * 10000) / 10.0,
        'value_type' => 'integer',
        'name' => $definition['id'],
      );
    }

    $return = array(
      'results' => $results,
      'version' => self::getVersion(),
      'ts' => time(),
    );

    return $return;

  }

  /**
   * Encrypt the results.
   *
   * @param array $data
   *   The data.
   *
   * @return array
   *   The encrypted data.
   *
   * @throws Ex\CryptoException
   */
  private function encrypt(array $data) {

        if (!class_exists(Key::class)) {
            $data['encryptionNotSupported'] = TRUE;
            return $data;
        }
        try {
            $config = $this->config('factorial_monitoring_connector.settings');
            $key = $config->get('key');
            if (empty($key)) {
                $key = 'def0000017265d5ce1429e6987748d0ab48b35dade5626b04ec1dc4d724f8963192f90309327603758736a64c632a33aa90135f0b6d08e6bbee90063ff45ca9acdabf1ba';
            }
            //Load key into a Key object using Key's loadFromAsciiSafeString static method.
            $key = Key::loadFromAsciiSafeString($key);
            //Return the JSON representation of the data.
            $to_encrypt = json_encode($data['results']);
            //Encrypt the data with the Key, using defuse/php-encryption
            $cipherText = Crypto::encrypt($to_encrypt, $key);
            $data['results'] = $cipherText;
            $data['encrypted'] = TRUE;
        }
        catch (Ex\EnvironmentIsBrokenException $e) {
            throw new Ex\CryptoException($e->getMessage(), $e->getCode(), $e);
        }
        catch (Ex\BadFormatException $e) {
            throw new Ex\CryptoException($e->getMessage(), $e->getCode(), $e);
        }
        return $data;
   }

}
