<?php

namespace Drupal\factorial_monitoring_connector\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

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
      $results = array_merge($results, $plugin->collect());
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
   */
  private function encrypt(array $data) {
    if (!extension_loaded('mcrypt')) {
      $data['encryptionNotSupported'] = TRUE;
      return $data;
    }

    $to_encrypt = json_encode($data['results']);
    $cipher = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
    $config = $this->config('factorial_monitoring_connector.settings');

    /*
    for 256 bit AES encryption key size should be of 32 bytes (256 bits)
    for 128 bit AES encryption key size should be of 16 bytes (128 bits)
    here i am doing 256-bit AES encryption
    choose a strong key
     */
    $key256 = $config->get('key256');
    if (empty($key)) {
      $key256 = '12345678901234561234567890123456';
    }
    /*
    for 128 bit Rijndael encryption, initialization vector (iv) size should be 16 bytes
    for 256 bit Rijndael encryption, initialization vector (iv) size should be 32 bytes
    here I have chosen 128 bit Rijndael encyrption, so $iv size is 16 bytes
     */
    $iv = $config->get('iv');
    if (empty($iv)) {
      $iv = '1234567890123456';
    }

    mcrypt_generic_init($cipher, $key256, $iv);
    // PHP pads with NULL bytes if $plainText is not a multiple of the block size.
    $cipherText256 = mcrypt_generic($cipher, $to_encrypt);
    mcrypt_generic_deinit($cipher);
    /*
    $cipherHexText256 stores encrypted text in hex
    we will be decrypting data stored in $cipherHexText256 from node js
     */
    $cipherHexText256 = bin2hex($cipherText256);

    $data['results'] = $cipherHexText256;
    $data['encrypted'] = TRUE;
    return $data;
  }

}
