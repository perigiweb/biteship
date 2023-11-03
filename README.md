# PHP Client for Biteship API

A PHP client for accessing [Biteship](https://biteship.com) API

```shell
composer require perigiweb/biteship
```

## How To Use

```php
use Perigi\Biteship\Client;

$biteshipApiKey = '';
$client = new Client($biteshipApiKey);

$availableCouriers = $client->getCouriers();
```