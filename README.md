# Telegram Post

Delayed posting in a Telegram channel via the web interface (Laravel / Vue 3 / Inertia.js)

## Interface preview

### Preview Tab
![Preview Tab](https://github.com/eve-at/telegram-post/blob/master/public/images/post-preview.jpg?raw=true)
### Shedule Tab
![Shedule Tab](https://github.com/eve-at/telegram-post/blob/master/public/images/post-sheduling.jpg?raw=true)
### How It Looks On Telegram After Publication
![Published Post on Telegram](https://github.com/eve-at/telegram-post/blob/master/public/images/how-it-looks-on-telegram.jpg?raw=true)

## Installation
- Clone & `composer install`
- [Obtain your Telegram Bot Token](https://core.telegram.org/bots/tutorial#obtain-your-bot-token) and put it into `TELEGRAM_BOT_TOKEN` .env field
- Get your Telegram Channel ID and put it into `TELEGRAM_CHANNEL_ID` .env field

## How to find your Telegram Channel ID
- Go to https://web.telegram.org and log in
- Click on the channel you want to manage. You'll be redirected to https://web.telegram.org/a/#-100XXXXXXXXX. Where **-100XXXXXXXXX** is the ID of your channel.

If your url looks like .../c12112121212_17878787878787878, do the following:
- Get the part between the first letter ("c") and the underscore. In my example you'll get **12112121212**
- Prefix it with a -100 so **-10012112121212** is your channel id.

Create the symbolic link for your media storage
```
php artisan storage:link
```

## Run in local

1. Install the dependencies
```
composer install
```

2. Start the server
```
./vendor/bin/sail up
```

3. In another terminal window execute
```
npm run dev
```


To connect to the MySQL server execute:
```
mysql -h 127.0.0.1 -P 3307 -u db -p
```
