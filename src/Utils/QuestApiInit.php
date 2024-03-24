<?php

namespace QuestApi\Utils;

use QuestApi\Controllers\DatabaseController;
use QuestApi\Controllers\ConfigController;
use Exception;

class QuestApiInit
{
    public function __construct()
    {
        $this->initDB();
    }

    private function initDB()
    {
        $db = DatabaseController::getConnection();
        try {
            $db->query("SELECT 1");
        } catch (Exception $e) {
            echo "Database connection failed: " . $e->getMessage() . PHP_EOL;
            die();
        }

        $table = $db->queryFirstRow("SHOW TABLES LIKE 'discordlink'");

        $config = (new ConfigController)->get();
        $discordLinkEnabled = (isset($config['discordlinkFeature'])) ? $config['discordlinkFeature'] : false;

        if ($discordLinkEnabled === false) {
            return;
        }

        if (empty($table)) {
            echo "Table discordlink does not exist. Creating table..." . PHP_EOL;

            try {
                $db->query("CREATE TABLE discordlink (
                    id INT(255) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    eos_id VARCHAR(255) NOT NULL,
                    discord_id VARCHAR(255) NULL,
                    discord_name VARCHAR(255) NULL,
                    activationcode INT(255) NULL,
                    timestamp INT(255) NOT NULL,
                    UNIQUE INDEX eos_id (eos_id),
	                UNIQUE INDEX discord_id (discord_id)
                )");

                echo "Table discordlink created." . PHP_EOL;
            } catch (Exception $e) {
                echo "Table creation failed: " . $e->getMessage() . PHP_EOL;
                die();
            }
        }

        try {
            // Check if lethalquests tables exists - Else quit
            $table = $db->query("SHOW TABLES LIKE 'lethalquestsascended%'");

            if (empty($table)) {
                echo "Tables for lethalquests does not exist in this database. Quitting..." . PHP_EOL;
                die();
            }

            echo "Table check completed." . PHP_EOL;
        } catch (Exception $e) {
            echo "Table check failed: " . $e->getMessage() . PHP_EOL;
            die();
        }

        echo "Database checks completed." . PHP_EOL;
    }
}
