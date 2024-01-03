# Telegram Post

Post into Telegram channel via Web interface

## Installation
- Clone & `composer install`
- [Obtain your Telegram Bot Token](https://core.telegram.org/bots/tutorial#obtain-your-bot-token) and put it into TELEGRAM_BOT_TOKEN .env field
- Get your Telegram Channel ID and put it into TELEGRAM_CHANNEL_ID .env field

## How to find your Telegram Channel ID
- Go to https://web.telegram.org and log in
- Click on the channel you want to manage. You'll be redirected to https://web.telegram.org/a/#-100XXXXXXXXX. Where **-100XXXXXXXXX** is the ID of your channel.

If your url looks like .../c12112121212_17878787878787878, do the following:
- Get the part between the first letter ("c") and the underscore. In my example you'll get **12112121212**
- Prefix it with a -100 so **-10012112121212** is your channel id.

WIP