# ArkAscendedQuestApi

## Description

This is an api that provides endpoints for Lethal's lethalquests ascended plugin.
I've made this to go hand in hand with The-God-Of-Noise's UI in Ark:Survival Ascended - But you can use it for whatever you want. (only your imagination is the limit.)

Provided endpoints:
- {eos_id}/currentquests (Show the current available quests for a player)
- {eos_id}/completedquests (Shows the completed quests for a player)
- {eos_id}/leaderboards (shows a leaderboard)
- {eos_id}/statistics (shows various player statistics)
- {eos_id}/trackers (shows all tracked stats for a player)

## Prerequisites

- PHP
- Composer

## Installation

Follow these steps to get the project up and running:

1. Clone the repository or download the source code to your server.

    ```bash
    git clone https://github.com/nissemayn/ArkAscendedQuestApi.git
    ```

2. Navigate to the project directory.

    ```bash
    cd yourproject
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

Contributing
Contributions are welcome! For major changes, please open an issue first to discuss what you would like to change.

License
MIT