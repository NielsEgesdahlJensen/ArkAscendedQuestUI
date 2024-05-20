<?php

namespace QuestApi\Helpers;

use QuestApi\Controllers\ConfigController;
use React\Http\Browser;

class ModSurveilance
{
    private static string $curseforgeBaseUri = 'https://api.curseforge.com';

    public static function CheckAllMods()
    {
        $config = (new ConfigController)->get();
        $modSurveilce = $config['ModSurveilce'];

        $modIds = array_keys($modSurveilce['mods']);

        $requestBody = [
            'modIds' => $modIds,
            'filterPcOnly' => false
        ];

        $client = new Browser();

        $response = $client->post(
            self::$curseforgeBaseUri . '/v1/mods',
            [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'x-api-key' => $modSurveilce['apiKey']
            ],
            json_encode($requestBody)
        )->then(
            function ($response) use ($modSurveilce, $config) {
                $mods = json_decode($response->getBody())->data;

                foreach ($mods as $mod) {
                    $modId = $mod->id;
                    $latestFileId = $mod->latestFiles[0]->id;

                    $modInfo = $modSurveilce['mods'][$modId];

                    $changed = false;

                    if (!isset($modInfo['latestFileId'])) {
                        $changed = true;
                    }

                    // Check if the version has changed
                    elseif ($modInfo['latestFileId'] != $latestFileId) {
                        $changed = true;
                    }

                    if ($changed) {
                        //get changelog
                        $client = new Browser();
                        $response = $client->get(
                            self::$curseforgeBaseUri . '/v1/mods/' . $modId . '/files/' . $latestFileId . '/changelog',
                            [
                                'Content-Type' => 'application/json',
                                'x-api-key' => $modSurveilce['apiKey']
                            ]
                        )->then(
                            function ($response) use ($modId, $mod, $modSurveilce, $latestFileId) {
                                $changelog = json_decode($response->getBody())->data;
                                $changelog = str_replace('<br>', "\n", $changelog);
                                $changelog = strip_tags($changelog);
                                $changelog = htmlspecialchars_decode($changelog);

                                $data = [
                                    'content' => "",
                                    'embeds' => [
                                        [
                                            'title' => "Changelog",
                                            'description' => $changelog,
                                            'author' => [
                                                'name' => "New update for $mod->name",
                                            ],
                                            'thumbnail' => [
                                                'url' => $mod->logo->url
                                            ],
                                            'footer' => [
                                                'text' => 'Powered by ArkAscendedQuestApi'
                                            ],
                                            'timestamp' => date('Y-m-d\TH:i:s\Z'),
                                            "color" => 2354023,
                                        ],
                                    ],
                                    "username" => "ArkAscendedQuestApi",
                                ];

                                $client = new Browser();
                                $client->post($modSurveilce['discordWebhook'], [
                                    'Content-Type' => 'application/json'
                                ], json_encode($data, JSON_UNESCAPED_SLASHES))->then(
                                    function ($response) use ($modId, $latestFileId) {
                                        // get config
                                        $config = ConfigController::get();
                                        $config['ModSurveilce']['mods'][$modId]['latestFileId'] = $latestFileId;
                                        ConfigController::set($config);
                                    },
                                    function ($e) {
                                        echo $e->getMessage() . PHP_EOL;

                                        // get response body
                                        echo $e->getResponse()->getBody() . PHP_EOL;
                                    }
                                );
                            },
                        );
                    }
                }

                //Wait for all promises in the foreach loop to finish:



            },
            function ($e) {
                echo $e->getMessage();
            }
        );

        /*foreach ($modSurveilce['mods'] as $modId => $modInfo) {
            $client = new Browser();
            $response = $client->get(
                self::$curseforgeBaseUri . '/v1/mods/' . $modId,
                [
                    'Content-Type' => 'application/json',
                    'x-api-key' => $modSurveilce['apiKey']
                ]
            )->then(
                function ($response) use ($modId, $modInfo, $config, $modSurveilce) {
                    $changed = false;
                    $modJson = (json_decode($response->getBody()))->data;
                    $latestFileId = $modJson->latestFiles[0]->id;

                    if (!isset($modInfo['latestFileId'])) {
                        $changed = true;
                    }

                    // Check if the version has changed
                    elseif ($modInfo['latestFileId'] != $latestFileId) {
                        $changed = true;
                    }

                    if ($changed) {
                        echo "Mod $modId has changed. Updating config..." . PHP_EOL;
                        $config['ModSurveilce']['mods'][$modId]['latestFileId'] = $latestFileId;

                        //get changelog
                        $client = new Browser();
                        $response = $client->get(
                            self::$curseforgeBaseUri . '/v1/mods/' . $modId . '/files/' . $latestFileId . '/changelog',
                            [
                                'Content-Type' => 'application/json',
                                'x-api-key' => $modSurveilce['apiKey']
                            ]
                        )->then(
                            function ($response) use ($modId, $modJson, $config, $modSurveilce) {
                                $changelog = json_decode($response->getBody())->data;
                                echo "Changelog for mod $modId: $changelog" . PHP_EOL;

                                $data = [
                                    'content' => "Mod $modId has changed. New version: " . $modJson->latestFiles[0]->displayName . ". Changelog: $changelog"
                                ];

                                $client = new Browser();
                                $client->post($modSurveilce['discordWebhook'], [
                                    'Content-Type' => 'application/json'
                                ], json_encode($data))->then(
                                    function ($response) use ($config) {
                                        echo "Notification sent to Discord" . PHP_EOL;
                                        ConfigController::set($config);
                                    },
                                    function ($e) {
                                        echo $e->getMessage();
                                    }
                                );
                            },
                        );
                    }
                }
            );
        }*/
    }
}
