<?php

namespace Perigi\Biteship\Test;

use Perigi\Biteship\Client;
use PHPUnit\Framework\TestCase;

class PublicTrackingTest extends TestCase {

  public function testPublicTracking(){
    $config = include __DIR__ . '/config.php';
    $client = new Client($config['apiKey']);

    $response = $client->publicTracking($config['trackingWaybillId'], $config['trackingCourierCode']);

    print_r($response);

    $this->assertSame('array', gettype($response));
  }
}