# Unofficial PHP Client for Biteship API

This library is unofficial PHP client for accessing [Biteship](https://biteship.com) API

```shell
composer require perigiweb/biteship
```

## How To Use

```php
use Perigi\Biteship\Client;

$biteshipApiKey = '';
$client = new Client($biteshipApiKey);
```

## Supported Method

### Get Available Couriers

```php
$availableCouriers = $client->getCouriers();
```

### Search Area

```php
// single search https://biteship.com/id/docs/api/maps/retrieve_area_single
$areas = $client->searchArea($districtName);

// double search https://biteship.com/id/docs/api/maps/retrieve_area_double
$areas = $client->searchArea($districtName, 'double');

// areaId from double search result
$areas = $client->searchAreaById($areaId);
```

### Get Shipping Rates

```php
$origin = 'IDN...'; // area_id
$destination = 'IDN....'; // area_id
$couriers = 'jne,jnt,sicepat,anteraja';
$items = [$item];

// origin and destination can be an array of postal code
$origin = [
  'origin_postal_code' => 11122
];
$destination = [
  'destination_postal_code' => 53461
];

// or combination of latitude and longitude
$origin = [
  'origin_latitude' => 5.8474647464,
  'origin_longitude' => 7.57575757
];
$destination = [
  'destination_latitude' => 3.8474647464,
  'destination_longitude' => 5.57575757
];

$rates = $client->getRates($origin, $destination, $couriers, $items);
```

### Tracking

```php
// tracking by order id
$tracking = $client->tracking($biteshipOrderId);

// or tracking by waybill and courier code
$tracking = $client->publicTracking($waybillId, $courierCode);
```

### Order

Create, retrive, update and delete order not supported yet

More info on [Biteship API Docs](https://biteship.com/id/docs/intro)