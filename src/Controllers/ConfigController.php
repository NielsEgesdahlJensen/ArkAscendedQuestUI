<?php
namespace QuestApi\Controllers;

class ConfigController {
    public static function get() : array {
        $configFile = __DIR__."/../../config.json";
        if (file_exists($configFile)) {
            $config = json_decode(file_get_contents($configFile), true);
        }

        else {
            echo "Config file not found. Exiting...";
            die();
        }
        //print_r($config);
        return $config;
    }
}