# Configuration

Create a JSON file `config.json` containing the following keys:

|Key|Description|Example|
|-|-|-|
|course|Course identifier|INF19|
|language|JSON file path with language strings to use|lang/en.json|
|webhook_connections|Max. number of separate connections ([see Bot API](https://core.telegram.org/bots/api#setwebhook))|40|
|webhook_subscribe|Message types to subscribe to ([see Bot API](https://core.telegram.org/bots/api#setwebhook))|[]|
|online_timetable_url|URL to the rapla timetable|https://rapla.<domain>/rapla/calendar?key=...|
|online_timetable_discord_webhook|Forwards timetable update to a discord webhook|https://discord.com/api/webhooks/...|
|webhook_subscribe|Message types to subscribe to ([see Bot API](https://core.telegram.org/bots/api#setwebhook))|[]|
|webhook_url|URL to which Telegram will push messages|https://inf19bot.../hook.php|
|tracefile|Location of the trace file. Unset to disable tracing|trace|
|forward_err_to|Usernames to which the bot should forward critical errors|[notch]

## Secrets

- `secret/key` - secret used for accessing the web configuration
- `secret/tgtoken` - telegram token
- `secret/tick` - token the cron job callback will use
- `secret/mail.json` - JSON object containing the following keys (forwards all emails of which the subject starts with the course identifier)
    - `mailbox` - server to which the script should connect. requires curly braces around the value like `{imap.<hostname>.com}`
    - `email` - email account of the bot
    - `password` - email password of the bot
- `secret/bossmail.json` - JSON object containing the following keys (only forwards emails from `bossmail`)
    - `mailbox` - server to which the script should connect. requires curly braces around the value like `{imap.<hostname>.com}`
    - `email` - email account of the bot
    - `password` - email password of the bot
    - `bossmail` - fetch messages with this email as receiver

### Format

Only first line is read in - rest is ignored. Newlines will be stripped. 
