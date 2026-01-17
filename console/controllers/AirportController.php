<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use common\models\Airport;

/**
 * Imports airport data from a CSV file.
 */
class AirportController extends Controller
{
    /**
     * Import airports from a CSV file.
     *
     * @param string $filename Absolute path to CSV file
     * @return int
     */
    public function actionImport(string $filename): int
    {
        if (!file_exists($filename)) {
            $this->stderr("File not found: {$filename}\n");
            return ExitCode::NOINPUT;
        }

        $this->stdout("Importing airports from: {$filename}\n");

        $handle = fopen($filename, 'r');
        if ($handle === false) {
            $this->stderr("Unable to open file.\n");
            return ExitCode::CANTCREAT;
        }

        $transaction = Yii::$app->db->beginTransaction();

        try {
            $header = fgetcsv($handle); // Skip header row
            $count = 0;

            while (($row = fgetcsv($handle)) !== false) {
                [
                    $id,
                    $name,
                    $city,
                    $country,
                    $iata,
                    $icao,
                    $latitude,
                    $longitude
                ] = $row;

                if (!$latitude || !$longitude) {
                    continue;
                }

                // Upsert behavior
                $sql = <<<SQL
INSERT INTO airport (
    id, name, city, country, iata, icao, location
) VALUES (
    :id, :name, :city, :country, :iata, :icao, POINT(:lng, :lat)
)
ON DUPLICATE KEY UPDATE
name = VALUES(name),
city = VALUES(city),
country = VALUES(country),
iata = VALUES(iata),
icao = VALUES(icao),
location = VALUES(location)
SQL;
                Yii::$app->db->createCommand($sql, [
                        ':id'      => (int)$id,
                        ':name'    => $name,
                        ':city'    => $city,
                        ':country' => $country,
                        ':iata'    => $iata,
                        ':icao'    => $icao,
                        ':lat'     => (float)$latitude,
                        ':lng'     => (float)$longitude,
                        ])->execute();

                $count++;

                if ($count % 1000 === 0) {
                    $this->stdout("Imported {$count} airports...\n");
                }
            }

            fclose($handle);
            $transaction->commit();

            $this->stdout("Import complete. Total airports: {$count}\n");
            return ExitCode::OK;

        } catch (\Throwable $e) {
            $transaction->rollBack();
            fclose($handle);

            $this->stderr("Import failed: {$e->getMessage()}\n");
            Yii::error($e);
            return ExitCode::UNSPECIFIED_ERROR;
        }
    }
}

