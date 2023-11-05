<?php

namespace Perigi\Biteship\Test;

use Perigi\Biteship\Client;
use PHPUnit\Framework\TestCase;

class SearchAreaTest extends TestCase {

  public function testSearchArea(){
    $config = include __DIR__ . '/config.php';
    $client = new Client($config['apiKey']);
    $response = $client->searchArea('medayu');

    echo __METHOD__ . "\n";
    print_r($response);

    $this->assertSame('array', gettype($response));
  }

  public function testSearchByAreaId(){
    $config = include __DIR__ . '/config.php';
    $client = new Client($config['apiKey']);
    $response = $client->searchAreaById('IDNP3IDNC445IDND5615');

    echo __METHOD__ . "\n";
    print_r($response);

    $this->assertSame('array', gettype($response));
  }
}