<?php

namespace Perigi\Biteship\Test;

use Perigi\Biteship\Client;
use PHPUnit\Framework\TestCase;

class CourierTest extends TestCase {

  public function testGetCouriers(){
    $config = include __DIR__ . '/config.php';
    $client = new Client($config['apiKey']);

    $response = $client->getCouriers();

    print_r($response);

    $this->assertSame('array', gettype($response));
  }
}