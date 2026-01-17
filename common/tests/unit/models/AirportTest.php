<?php

namespace common\tests\unit\models;

use common\models\Airport;
use Codeception\Test\Unit;

class AirportTest extends Unit
{
private array $airports = [
    [
        'id' => 1,
        'name' => 'John F Kennedy International',
        'city' => 'New York',
        'country' => 'United States',
        'iata' => 'JFK',
        'icao' => 'KJFK',
        'location' => "POINT(-73.7781 40.6413)",
    ],
    [
        'id' => 2,
        'name' => 'Los Angeles International',
        'city' => 'Los Angeles',
        'country' => 'United States',
        'iata' => 'LAX',
        'icao' => 'KLAX',
        'location' => "POINT(-118.4085 33.9416)",
    ],
    [
        'id' => 3,
        'name' => 'Toronto Pearson',
        'city' => 'Toronto',
        'country' => 'Canada',
        'iata' => 'YYZ',
        'icao' => 'CYYZ',
        'location' => "POINT(-79.6306 43.6777)",
    ],
];

protected function _before(): void
{
    $db = \Yii::$app->db;

    // Ensure clean state
    $db->createCommand()->truncateTable('airport')->execute();

    foreach ($this->airports as $airport) {
        $db->createCommand()->insert('airport', [
            'id'      => $airport['id'],
            'name'    => $airport['name'],
            'city'    => $airport['city'],
            'country' => $airport['country'],
            'iata'    => $airport['iata'],
            'icao'    => $airport['icao'],
            'location'=> new \yii\db\Expression(
                "ST_GeomFromText('{$airport['location']}')"
            ),
        ])->execute();
    }
}

protected function _after(): void
{
    \Yii::$app->db->createCommand()->truncateTable('airport')->execute();
}



    /**
     * Basic sanity check that airports exist.
     */
    public function testAirportFixturesLoaded(): void
    {
        $airport = Airport::find()->one();

        $this->assertNotNull($airport);
        $this->assertNotEmpty($airport->iata);
        $this->assertNotEmpty($airport->location);
    }

    /**
     * Problem 2
     * Test airports within radius of a coordinate.
     */
    public function testFindWithinRadius(): void
    {
        // JFK approx location
        $lat = 40.6413;
        $lng = -73.7781;

        $airports = Airport::findWithinRadius($lat, $lng, 50);

        $this->assertIsArray($airports);
        $this->assertNotEmpty($airports);

        foreach ($airports as $airport) {
            $this->assertArrayHasKey('iata', $airport);
            $this->assertArrayHasKey('distance_miles', $airport);
            $this->assertLessThanOrEqual(50, $airport['distance_miles']);
        }
    }

    /**
     * Problem 3
     * Distance between two known airports.
     */
    public function testDistanceBetweenIata(): void
    {
        $distance = Airport::distanceBetweenIata('JFK', 'LAX');

        $this->assertNotNull($distance);
        $this->assertIsFloat($distance);

        // JFK ↔ LAX ≈ 2475 miles (allow variance)
        $this->assertGreaterThan(2300, $distance);
        $this->assertLessThan(2600, $distance);
    }

    /**
     * Problem 3
     * Invalid airport returns null.
     */
    public function testDistanceBetweenInvalidIata(): void
    {
        $distance = Airport::distanceBetweenIata('XXX', 'YYY');

        $this->assertNull($distance);
    }

    /**
     * Problem 4
     * Closest airports between two countries.
     */
    public function testClosestBetweenCountries(): void
    {
        $result = Airport::closestBetweenCountries(
            'United States',
            'Canada'
        );

        $this->assertIsArray($result);
        $this->assertArrayHasKey('airport_1', $result);
        $this->assertArrayHasKey('airport_2', $result);
        $this->assertArrayHasKey('distance_miles', $result);

        $this->assertNotEmpty($result['airport_1']);
        $this->assertNotEmpty($result['airport_2']);
        $this->assertGreaterThan(0, $result['distance_miles']);
    }

    /**
     * Problem 5 helper
     * Neighbors within max distance.
     */
    public function testNeighborsWithinDistance(): void
    {
        $airport = Airport::findOne(['iata' => 'JFK']);

        $this->assertNotNull($airport);

        $neighbors = Airport::neighborsWithinDistance(
            $airport->id,
            300
        );

        $this->assertIsArray($neighbors);

        foreach ($neighbors as $neighbor) {
            $this->assertArrayHasKey('iata', $neighbor);
            $this->assertArrayHasKey('distance_miles', $neighbor);
            $this->assertLessThanOrEqual(300, $neighbor['distance_miles']);
        }
    }
}

