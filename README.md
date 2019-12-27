# inf19bot

A Telegram bot for inf19.

## Features

- [X] Easily extendable
- [X] Language files

## Configuration

Have a look at [Secret Configuration](secret/README.md) to find out how to setup Telegram and API Tokens.

Create a JSON file containing the following keys:

|Key|Description|Example|
|-|-|-|
|language|JSON file with language strings to use|lang/en.json|
|webhook_connections|Max. number of separate connections ([see Bot API](https://core.telegram.org/bots/api#setwebhook))|40|
|webhook_subscribe|Message types to subscribe to ([see Bot API](https://core.telegram.org/bots/api#setwebhook))|[]|
|webhook_url|URL to which Telegram will push messages|https://inf19bot...de/src/hook.php|
|tracefile|Location of the trace file. Unset to disable tracing|trace|
