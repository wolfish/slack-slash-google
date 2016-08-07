# Slack Slash Google

## What is this?
It's a *very* simple tool that answers requests with [Google Custom Search](https://cse.google.com) response using also some parsed parameters.
Response is JSON formatted to be used on [Slack](https://slack.com) as Slash command letting users make a quick in-channel google search, or display images/gifs.

## What is it good for?
By default, this tool returns one image as response, so it can be used to spice the conversation with some memes/etc.

It parses commands provided after hashtags (full list of them lower in this doc), so by adding `#link #multi` in beginning of your query you can get first ten results from google.

There are many other ways to use this, and you can easliy configure default behavior.
Also, by configuring the CSE itself you can limit searches to one site, or make results from certain site priority/blocked, the possibilities are endless.

## Available commands
- `#[number]` - get [number] result of the search (or if multiple results - start with this number)
- `#image` - get result as an image (default)
- `#gif` - get result as an GIF image
- `#link` - get result as standard link
- `#one` - get one result (default)
- `#multi` - get multiple results (10 by default)

You can of course join commands, so `/xyz #multi #20 #link best cars` gives you list of ten links of "best cars" starting from 20 result.

**Every text before or between commands is ignored** `/xyz not #image working #one but this is` will be searching for "but this is" with one image result 

## Requirements
* PHP 5.5 or higher (also tested with PHP 7)
* HTTPS (required by Slack, if you don't have yet get certificate by Let's Encrypt or CloudFlare)
* Slack account with permission to create Slash command
* Google account to get Dev key, and configure Custom Search Engine

## Installation
1. Create new dir on your server and download [latest release](https://github.com/wolfish/slack-slash-google/releases)
2. Unpack it, set proper permissions, create new virtual host or just get URL pointing to it
2. Create new "Slash command" in your Slack team
  - Go to https://**YOUR-TEAM**.slack.com/apps/manage/custom-integrations
  - Select "Slash commands" and "Add configuration"
  - Set "URL" to one thats pointing to your new dir with dowloaded app (**IMPORTANT** it HAVE to be HTTPS!)
  - Set "Method" to POST
  - Copy value from "Token" and paste it to `src/Config.php.dist` to `const SLACK_TOKEN = 'your_slack_slash_command_token';`
  - Set rest of settings however you want
3. Enable "Google Custom Search API" and get developer key
  - Login to https://console.developers.google.com go to "Library", search for "custom search api" select it and **ENABLE** it
  - Go to "Credentials" and "Create credentials -> API key" then select "Server key"
  - You can give it a name, and IP restrictions. After that copy the generated key and paste it in `src/Config.php.dist` as value in `const GOOGLE_DEV_KEY = 'your_google_dev_key';`
4. Create an CSE
  - Login to https://cse.google.com and create new custom search
  - You have to add at least one website that will be searched for results as priority
  - After adding move to configuration panel
  - Set "Image search" to ON (**IMPORTANT** if you don't do it image results will not be shown)
  - In "Sites to search" set select to "Search whole web (...)" (**IMPORTANT** otherwise only selected website will be searched for results)
  - Click on "Search ID" and copy key to `src/Config.php.dist` as value in `const GOOGLE_CX_KEY = 'your_google_custom_search_key';`
5. Edit `src/Config.php.dist` and set rest of the settings as you require (or leave them as default). Then change file name to `Config.php`

It should work after that, you can of course modify CSE settings to your requirements, like results only from one site, keywords, etc.

The slash command itself is customizable, so you can name it as you wish and add proper description.

Test it with `/yourcommand your google query` on Slack

If somethings wrong, you can debug it by running app in console and use first parameter as the query, like `php index.php "your google query"`

If something still doesn't work, or you noticed a bad thing, [create new issue](https://github.com/wolfish/slack-slash-google/issues)

## Contributing
Any feedback or code contribution that is useful is welcome.

Current TODO's are:
* More commands providing rest of CSE parameters
* User control (allowed/banned users)
* Time/query count limitation per user 
* Paramter to control `in_channel` like `#pub` and `#priv`

## License
Project released under the MIT license. See the LICENSE file for more information.
