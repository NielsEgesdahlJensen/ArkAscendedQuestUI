# ArkAscendedQuestApi

## Description

Ark Ascended Quest API is a PHP-based application designed to provide endpoints for Lethal's lethalquests ascended plugin. Originally created to complement The-God-Of-Noise's UI in Ark: Survival Ascended, this API can be adapted for various other uses.

The API provides the following endpoints:

GET requests
- `{eos_id}/currentquests`: Displays the current available quests for a player
- `{eos_id}/completedquests`: Shows the completed quests for a player
- `{eos_id}/leaderboards`: Displays a leaderboard
- `{eos_id}/statistics`: Provides various player statistics
- `{eos_id}/trackers`: Shows all tracked stats for a player
- `{eos_id}/quest/{quest_id}`: Shows tracked data for a given quest
- `content/{file}`: Option to self host static files in the content dir like dynamic server settings.

POST requests
- `{eos_id}/discordlink`: used in The-God-Of-Noise's UI to link a Discord user to an eos id. (Discord bot with functionality for this may or may not be released.) Feature is turned off by default in config.

## Prerequisites

- PHP (https://www.php.net/downloads.php)
- - Modules needed:
- - mysqli
- - zip
- Composer (https://getcomposer.org/download/) - Be sure to add the checkbox `Add PHP to path` during installation.

See Windows installation to get help: [Windows Installation](windows-installation.md)

### Enabeling modules in php

- Locate your current `php.ini` file. Typically in `c:\php\php.ini` on windows.
- Find the lines with `;extension=mysqli` and `;extension=zip` and uncomment them. (Remove the ;)
- Save the file.

## Installation

Follow these steps to get the project up and running:

1. Clone the repository or download the source code to your server.

    ```bash
    git clone https://github.com/nissemayn/ArkAscendedQuestApi.git
    ```

2. Navigate to the project directory.

    ```bash
    cd ArkAscendedQuestApi
    ```

3. Install the necessary dependencies using Composer.

    ```bash
    composer install
    ```

4. Copy the `example.config.json` to `config.json` and make necessary changes according to your setup.


## Usage

To start the API service, run the following command:

```bash
php index.php
```

For Windows users, .bat scripts are provided for installing and running the service.