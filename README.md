# PHPTeleBotWrt
- Telegram bot framework written in PHP for OpenWRT

## Features

* Support Long Polling and Webhook.
* Proxy List (Openclash Proxies)
* Rules List (Openclash Rules)
* Openclash Information
* Simple, easy to use.
* Inline Command
* Speedtest
* Sysinfo
* Memory
* Vnstat
* My IP
* Aria2
* MyXL

# 📷 Screenshots
##### Edit `databot` with your own Bot Token
![bottoken](https://i.ibb.co/vP7csgQ/TokenBot.png)
##### Starting Bot
![Startingbot](https://i.ibb.co/mcYqq3S/startbot.png)
##### /start | /cmdlist
![Start cmdlist](https://i.ibb.co/y4wqFwb/cmdlist.png)
##### /memory
![Memory](https://i.ibb.co/cwQ8m1C/memory.png)
##### /myip
![Myip](https://i.ibb.co/PQVB3DH/myip.png)
##### /myxl `number`
![MyXL](https://i.ibb.co/bBMf0rg/myxl.png)
##### /proxies
![Proxies](https://i.ibb.co/0fmXhjX/proxies.png)
##### /rules
![Rules](https://i.ibb.co/8DtrH3n/rules.png)
##### /speedtest `(depend on what speedtest installed)`
![Speedtest](https://i.ibb.co/r3cV90Y/speedtest.png)
##### /sysinfo
![sysinfo](https://i.ibb.co/2tqS3cM/sysinfo.png)
##### /vnstat `-d or -h or -m`
![sysinfo](https://i.ibb.co/0ycJhvP/vnstat.png)

## Requirements
- git
- screen
- php8-cli
- php8-mod-curl
- Telegram Bot API Token - Talk to [@BotFather](https://telegram.me/@BotFather)

## Installation

### Install from Terminal
1. Open Terminal/TTYD or apps like this.

2. Run commands below to install requirements on your OpenWRT:

```sh
opkg update
opkg install git git-http php8-cli php8-mod-curl
git clone https://github.com/helmiau/PHPTeleBotWrt && chmod +x PHPTeleBotWrt/src/plugins/*.sh
```

>  Skip opkg update and opkg install if you using HelmiWrt OS 2022-10-22 builds or newer.

3. Open your telegram account to get Telegram Bot API Token from [@BotFather](https://telegram.me/@BotFather)
4. Copy your Telegram Bot API Token and Bot Username
5. Run command below

```sh
cd PHPTeleBotWrt
cat "5227493446:AAGN1BeLV0I_7KIAyq_4aE6BZfH_fXq9yGQ" > databot
cat "botUsername" >> databot
```

## Usage
### About `databot' file inside PHPTeleBotWrt path
This file contain your Telegram Bot API Token and Bot Username. You must update this file if you want to change to another bot.

Start Screen
```sh
screen -S bot
```

Enter **PHPTeleBotWrt** Directory
```sh
cd PHPTeleBotWrt
```

Start bot
```sh
php8-cli index.php
```

Start bot in background
```sh
php8-cli index.php &>/dev/null &
```

Check bot runner in background
```sh
jobs
```
Then screen will show
```sh
root@HelmiWrt  on git:master x$~/PHPTeleBotWrt php8-cli index.php &>/dev/null &
[1] 15530 <<------- THIS IS JOB ID
 ^-------- THIS IS JOB NUMBER
 
root@HelmiWrt  on git:master x$~/PHPTeleBotWrt jobs
[1]  + running    php8-cli index.php &> /dev/null
 ^-------- THIS IS JOB NUMBER
```

Stop bot in background
```sh
kill job_id_from_check_bot_runner_command
```

or 

```sh
kill %job_number_from_check_bot_runner_command
```

Auto Start Bot after reboot / internet off
```sh
add scheduled tasks
*/5 * * * * cd PHPTeleBotWrt && php8-cli index.php
```

### Follow Installation Tutorial from Youtube (php7)
[Install OpenWrt PHP Telegram Bot By XppaiWRT | PHP7 REYRE-STB
](https://www.youtube.com/watch?v=JJPozNreVE0&lc=Ugy_OosDmlWRERUgvB94AaABAg.9iCzkvv1lxu9iV-s6tpDnO)

## Commands
Commands list
 * /aria2add      | Add task
 * /aria2stats    | Aria2 status
 * /aria2pause    | Pause all
 * /aria2resume   | Resume all
 * /oc        | OC Information
 * /proxies   | Proxies status
 * /rules     | Rule list
 * /vnstat    | Bandwidth usage
 * /memory    | Memory status
 * /myip      | Get ip details
 * /myxl 087x | MyXL Info
 * /speedtest | Speedtest
 * /ping      | Ping bot
 * /sysinfo   | System Information

## Tested on
* [**Reyre Firmware OC OnLy 28.09.22**](https://www.youtube.com/watch?v=vtjw38V2ybA) -> Speedtest bug
* [**Reyre Firmware OC OnLy 27.10.22**](https://www.youtube.com/watch?v=0KWgy6P2PVYA) -> Speedtest Fixed | vnstat Bug
* [**Reyre Firmware OC OnLy 06.11.22**](https://www.youtube.com/watch?v=SBHcJJC8ln0) -> Working Perfectly

## How To Update PHPTeleBotWrt
```sh
git reset --hard
git pull
chmod +x src/plugins/*.sh
```
