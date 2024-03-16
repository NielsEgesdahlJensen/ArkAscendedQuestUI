<?php

namespace QuestApi\Controllers;

use MeekroDB;
use QuestApi\Controllers\ConfigController;

class DatabaseController extends MeekroDB {
    // Singleton
    private static $_db = NULL;
    
    public static function getConnection(){
        if (self::$_db === NULL) {
            self::$_db = new self();
        }
        return self::$_db;
    }

    private ?MeekroDB $connection = NULL;

    private function __construct() {
        $config = (new ConfigController)->get();
        $databaseInfo = $config['database'];

        $this->connection = parent::__construct(
            $databaseInfo['dbHost'],
            $databaseInfo['dbUser'],
            $databaseInfo['dbPassword'],
            $databaseInfo['dbName'],
            $databaseInfo['dbPort'],
            $databaseInfo['dbEncoding'],
        );
    }
}