<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%airport}}`.
 */
class m260117_161854_create_airport_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $sql = <<<SQL
CREATE TABLE airport (
    id INT PRIMARY KEY,
    name VARCHAR(255),
    city VARCHAR(255),
    country VARCHAR(100),
    iata CHAR(3),
    icao CHAR(4),
    location POINT NOT NULL,
    SPATIAL INDEX idx_location (location),
    INDEX idx_country (country)
) ENGINE=InnoDB;
SQL;
        Yii::$app->db->createCommand($sql)->execute();
        $sql = <<<SQL
ALTER TABLE airport
ADD UNIQUE KEY uniq_iata (iata);
SQL;
        Yii::$app->db->createCommand($sql)->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $sql = <<<SQL
drop table airport
SQL;
        Yii::$app->db->createCommand($sql)->execute();
    }
}
