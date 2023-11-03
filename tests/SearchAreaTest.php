<?php

namespace Perigi\Biteship\Test;

use Perigi\Biteship\Client;
use PHPUnit\Framework\TestCase;

class SearchAreaTest extends TestCase {

  public function testSearchArea(){
    $config = include __DIR__ . '/config.php';
    $client = new Client($config['apiKey']);
    $response = $client->searchSingleArea('wanadadi');

    print_r($response);
    $this->assertSame('array', gettype($response));
  }
}