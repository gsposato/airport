<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\db\Query;

/**
 * This is the model class for table "airport".
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $city
 * @property string|null $country
 * @property string|null $iata
 * @property string|null $icao
 * @property string $location
 */
class Airport extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'airport';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'city', 'country', 'iata', 'icao'], 'default', 'value' => null],
            [['id', 'location'], 'required'],
            [['id'], 'integer'],
            [['location'], 'string'],
            [['name', 'city'], 'string', 'max' => 255],
            [['country'], 'string', 'max' => 100],
            [['iata'], 'string', 'max' => 3],
            [['icao'], 'string', 'max' => 4],
            [['id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'city' => 'City',
            'country' => 'Country',
            'iata' => 'Iata',
            'icao' => 'Icao',
            'location' => 'Location',
        ];
    }

    /**
     * Problem 2
     * Find airports within radius (miles) of a coordinate.
     */
    public static function findWithinRadius(
            float $lat,
            float $lng,
            float $radiusMiles
            ): array {
        $meters = $radiusMiles * 1609.34;
        $point = "POINT($lng $lat)";

        return self::find()
            ->select([
                    'id',
                    'name',
                    'city',
                    'country',
                    'iata',
                    'icao',
                    new Expression(
                        'ST_Distance_Sphere(location, ST_GeomFromText(:point)) / 1609.34 AS distance_miles'
                        ),
            ])
            ->where(
                    new Expression(
                        'ST_Distance_Sphere(location, ST_GeomFromText(:point)) <= :meters'
                        )
                   )
            ->addParams([
                    ':point' => $point,
                    ':meters' => $meters,
            ])
            ->orderBy('distance_miles')
            ->asArray()
            ->all();
    }

    /**
     * Problem 3
     * Distance (miles) between two airports by IATA code.
     */
    public static function distanceBetweenIata(
            string $fromIata,
            string $toIata
            ): ?float {
        $a = self::findOne(['iata' => $fromIata]);
        $b = self::findOne(['iata' => $toIata]);

        if (!$a || !$b) {
            return null;
        }

        return (new Query())
            ->select(
                    new Expression(
                        'ST_Distance_Sphere(a.location, b.location) / 1609.34'
                        )
                    )
            ->from(['a' => self::tableName()])
            ->innerJoin(['b' => self::tableName()], '1=1')
            ->where(['a.id' => $a->id, 'b.id' => $b->id])
            ->scalar();
    }

    /**
     * Problem 4
     * Closest airport pair between two countries.
     */
    public static function closestBetweenCountries(
            string $country1,
            string $country2
            ): ?array {
        $result = Yii::$app->db->createCommand(
                "
                SELECT
                a.iata AS airport_1,
                b.iata AS airport_2,
                ST_Distance_Sphere(a.location, b.location) / 1609.34 AS distance_miles
                FROM airport a
                JOIN airport b
                ON a.country = :c1
                AND b.country = :c2
                ORDER BY distance_miles ASC
                LIMIT 1
                "
                )->bindValues([
                    ':c1' => $country1,
                    ':c2' => $country2,
                ])->queryOne();

        return $result ?: null;
    }


    /**
     * Helper for routing:
     * Returns nearby airports within max distance (miles).
     */
    public static function neighborsWithinDistance(string $iata, float $maxMiles): array
    {
        $origin = self::findOne(['iata' => $iata]);

        if (!$origin) {
            return [];
        }

        $meters = $maxMiles * 1609.34;

        return (new \yii\db\Query())
            ->select([
                    'neighbor_id'   => 'b.id',
                    'neighbor_iata' => 'b.iata',
                    'distance_miles' => new \yii\db\Expression(
                        'ST_Distance_Sphere(a.location, b.location) / 1609.34'
                        ),
            ])
            ->from(['a' => self::tableName()])
            ->innerJoin(
                    ['b' => self::tableName()],
                    'a.id != b.id'
                    )
            ->where(['a.id' => $origin->id])
            ->andWhere(new \yii\db\Expression(
                        'ST_Distance_Sphere(a.location, b.location) <= :meters',
                        [':meters' => $meters]
                        ))
            ->orderBy(['distance_miles' => SORT_ASC])
            ->all();
    }

}
