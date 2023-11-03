<?php

namespace Perigi\Biteship;

use Exception;

use function curl_init;
use function curl_setopt_array;
use function curl_exec;
use function curl_error;
use function curl_getinfo;
use function curl_close;
use function json_decode;
use function json_encode;
use function sprintf;
use function strpos;

class Client {

  protected string $baseUri = 'https://api.biteship.com';
  protected string $apiVersion = 'v1';

  protected array $defaultCurlOptions = [
    \CURLOPT_CONNECTTIMEOUT => 10,
    \CURLOPT_RETURNTRANSFER => true,
    \CURLOPT_TIMEOUT        => 30,
    \CURLOPT_SSL_VERIFYHOST => 0,
    \CURLOPT_SSL_VERIFYPEER => false,
    \CURLOPT_FOLLOWLOCATION => false
  ];

  public function __construct(protected string $apiKey, protected array $curlOptions = [])
  {

  }

  public function getCouriers() : array {
    $response = $this->sendRequest('GET', '/couriers');

    return $response['couriers'];
  }

  public function searchSingleArea(string $query, string $countryCode = 'ID') : array
  {
    $response = $this->sendRequest('GET', '/maps/areas', [
      'countries' => $countryCode,
      'input' => $query,
      'type' => 'single'
    ]);

    return $response['areas'];
  }

  /**
   * @param string|array  $origin       string area_id or array [origin_postal_code => number] or [origin_latitude => number, origin_longitude => number]
   * @param string|array  $destination  string area_id or array [destination_postal_code => number] or [destination_latitude => number, destination_longitude => number]
   * @param string|array  $courierCodes string courier codes separate with coma or array courier code
   * @params ?string      $items        array of item['name', 'value', 'quantity', 'weight', 'height', 'width', 'length'], please see https://biteship.com/id/docs/api/rates/retrieve
   */
  public function getRates(
    string|array $origin,
    string|array $destination,
    string|array $courierCodes,
    ?array $items = null
  ) : ?array
  {
    if (is_string($origin)){
      $origin = ['origin_area_id' => $origin];
    }
    if (is_string($destination)){
      $destination = ['destination_area_id' => $destination];
    }
    if (is_array($courierCodes)){
      $courierCodes = implode(',', $courierCodes);
    }
    if (is_null($items)){
      $items = [
        [
          'name' => 'Product Name',
          'value' => 1,
          'quantity' => 1,
          'weight' => 1,
          'height' => 1,
          'width' => 1,
          'length' => 1
        ]
      ];
    }

    $params = $origin + $destination;
    $params['couriers'] = $courierCodes;
    $params['items'] = $items;

    return $this->sendRequest('POST', '/rates/couriers', $params);
  }

  public function tracking($biteshipOrderId)
  {
    return $this->sendRequest('GET', '/trackings/' . $biteshipOrderId);
  }

  public function publicTracking(string $waybillId, string $courierCode)
  {
    return $this->sendRequest('GET', '/trackings/' . $waybillId . '/couriers/' . $courierCode);
  }

  protected function sendRequest(string $method, string $endpoint, ?array $data = null) : mixed
  {
    $apiUrl = $this->getApiUrl($endpoint);
    if ($method === 'GET' && $data){
      $apiUrl .= '?' . http_build_query($data);
    }

    $curlOptions = $this->curlOptions + $this->defaultCurlOptions;
    $curlOptions[\CURLOPT_HEADER] = false;
    $curlOptions[\CURLOPT_CUSTOMREQUEST] = $method;
    $curlOptions[\CURLOPT_URL] = $apiUrl;
    $curlOptions[\CURLOPT_HTTPHEADER] = $this->getHeaders();

    if (\defined('CURLOPT_PROTOCOLS')) {
      $curlOptions[\CURLOPT_PROTOCOLS] = \CURLPROTO_HTTP | \CURLPROTO_HTTPS;
    }

    $curlOptions[\CURLOPT_HTTP_VERSION] = \CURL_HTTP_VERSION_1_1;

    if ($method === 'POST' && $data){
      $curlOptions[\CURLOPT_POSTFIELDS] = json_encode($data);
    }

    $handle = curl_init();
    curl_setopt_array($handle, $curlOptions);
    $result = curl_exec($handle);
    $curlErrNo = curl_errno($handle);
    $info = curl_getinfo($handle);
    curl_close($handle);

    if ($curlErrNo){
      $curlError = curl_error($handle);
      $message = sprintf(
        'cURL error %s: %s (%s)',
        $curlErrNo,
        $curlError,
        'see https://curl.haxx.se/libcurl/c/libcurl-errors.html'
      );
      if (false === strpos($curlError, $apiUrl)){
        $message .= sprintf(' for %s', $apiUrl);
      }

      throw new Exception($message, 500);
    }

    $response = json_decode($result, true);

    if ($info['http_code'] === 200 && $response['success']){
      return $response;
    }

    if (isset($response['error'])){
      $message = '';
      if ($response['code']){
        $message .= sprintf('API Error %s: ', $response['code']);
      }
      $message .= sprintf('%s', $response['error']);
    } else {
      $message = sprintf('HTTP Response Code: %s', $info['http_code']);
    }

    throw new Exception($message, $info['http_code'] === 200 ? 500:$info['http_code']);
  }

  protected function getHeaders() : array {
    return [
      'Host: ' . parse_url($this->baseUri, PHP_URL_HOST),
      'Content-Type: application/json',
      'Accept: application/json',
      'Authorization: Bearer ' . $this->apiKey
    ];
  }

  protected function getApiUrl(string $endPoint) : string {
    return $this->baseUri . '/' . $this->apiVersion . '/' . ltrim($endPoint, '/');
  }
}