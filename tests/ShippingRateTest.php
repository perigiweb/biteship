<?php

namespace Perigi\Biteship\Test;

use Perigi\Biteship\Client;
use PHPUnit\Framework\TestCase;

class ShippingRateTest extends TestCase {

  public function testGetRates(){
    $config = include __DIR__ . '/config.php';
    $client = new Client($config['apiKey']);

    $response = $client->getRates($config['origin'], $config['destination'], $config['couriers']);

    print_r($response);

    $this->assertSame('array', gettype($response));
  }
}