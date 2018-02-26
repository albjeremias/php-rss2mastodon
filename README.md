# rss2mastodon

Update your [Mastodon](https://github.com/tootsuite/mastodon) with accounts with RSS feeds.

![Join Mastodon](https://files.mastodon.social/site_uploads/files/000/000/001/original/DN5wMUeVQAENPwp.jpg_large.jpeg)
## Overview

1. Uses [PHP-CLI](http://php.net/manual/en/features.commandline.usage.php) and [Composer](https://getcomposer.org). 
2. No database or server required
3. Fast, secure, reliable and easy to maintain
4. Fully extendable

## How-to

1. Install php7-cli 
2. Install [Composer](https://getcomposer.org)
3. Copy the config_example.json to config.json
4. Edit config.json to your own needs
5. Run with the command to sync rss with mastodon:
 ```
  $ composer sync
 ```
 
## Q&A

##### Q: Where do I get my access token for mastodon??
##### A: Get your token here: [token generator](https://takahashim.github.io/mastodon-access-token/?code=1ab312089d27d2bd6cb9078484220bbb9baf4c5aeec25d25b6fe159c9e3aad02)

##### Q: I want to shorten my links...
##### A: get an api key here: [tiny-url.info](http://www.tiny-url.info/request_api_key.html) and set it up on the config