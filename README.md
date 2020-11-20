# inf19bot

A Telegram bot for inf19.

## Features

- [X] Easily extendable
- [X] Language files
- [X] Run commands in intervals

## Installation

1. Clone project to server. [git-ftp](https://github.com/git-ftp/git-ftp) has proven to be a good choice for this.

``` bash
git clone https://github.com/lausek/inf19bot
cd inf19bot

git config git-ftp.url <ftp_host>
git config git-ftp.user <ftp_user>
git config git-ftp.password <ftp_password>

# optional
git config git-ftp.remote_root <ftp_subdirectory>

git ftp init
```

2. Create `data/config.json`. See [Configuration](./data/README.md).

3. Setup Telegram webhook

``` bash
curl <host>/setup.php --data "key=<secret/key>&active=1"
```

## Configuration

Everything is configured inside `data/`. See [Configuration](./data/README.md).

## Development

``` bash
# build docker image
sudo docker build -t inf19bot .

# run server
sudo ./run local
```
