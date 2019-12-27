# Configuration

Create a JSON file `config.json` containing the following keys:

|Key|Description|Example|
|-|-|-|
|language|JSON file path with language strings to use|lang/en.json|
|webhook_connections|Max. number of separate connections ([see Bot API](https://core.telegram.org/bots/api#setwebhook))|40|
|webhook_subscribe|Message types to subscribe to ([see Bot API](https://core.telegram.org/bots/api#setwebhook))|[]|
|webhook_url|URL to which Telegram will push messages|https://inf19bot.../hook.php|
|tracefile|Location of the trace file. Unset to disable tracing|trace|

## Secrets

- [key] `secret/key` - secret used for accessing the web configuration
- [tgtoken] `secret/tgtoken` - telegram token

### Format

Only first line is read in - rest is ignored. Newlines will be stripped. 
