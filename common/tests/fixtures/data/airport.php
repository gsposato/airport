<?php

return [
    [
        'id' => 1,
        'name' => 'John F Kennedy International',
        'city' => 'New York',
        'country' => 'United States',
        'iata' => 'JFK',
        'icao' => 'KJFK',
        'location' => new \yii\db\Expression("ST_GeomFromText('POINT(-73.7781 40.6413)')"),
    ],
    [
        'id' => 2,
        'name' => 'Los Angeles International',
        'city' => 'Los Angeles',
        'country' => 'United States',
        'iata' => 'LAX',
        'icao' => 'KLAX',
        'location' => new \yii\db\Expression("ST_GeomFromText('POINT(-118.4085 33.9416)')"),
    ],
    [
        'id' => 3,
        'name' => 'Toronto Pearson',
        'city' => 'Toronto',
        'country' => 'Canada',
        'iata' => 'YYZ',
        'icao' => 'CYYZ',
        'location' => new \yii\db\Expression("ST_GeomFromText('POINT(-79.6306 43.6777)')"),
    ],
];

