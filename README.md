# Foreign rates plugin for WP
Foreign Exchange Rates using exchangeratesapi.io

### How it works?
This plugin is using API from exchangeratesapi.io and creating file inside /wp-content/plugins/foreignrates/json/data.json. 
Data download is scheduled with WP cron as event called 'load_api_hook'. It is scheduled to run once a day. Better option is usage of a server cron job, because WordPress cron is not reliable on sites with small loads. Categories and tags are created on plugin activation.

It contains 3 main components:

1. Shortcode
2. Widget
3. Exchange rates for posts

#### 1. Shortcode

It's intended to be in the sidebar using the following sample code [foreign_rates base="EUR" currencies="CAD,CHF,GBP,USD"].
Data is loaded directly from http://api.exchangeratesapi.io/latest. Other components use local file. It uses Jquery for calculations. 
Option for switching currencies and inline switch for currency names (not data in API) are included.

#### 2. Widget
Widget has its own options for currencies.

#### 3. Exchange rates for posts
Options are defined in the 'Foreign Rates Settings' page. It has 'Base currency', 'Convert to', 'Display in' and 'Enabled' options. 
Since the base rate in API is always 'EUR', it programatically calculates rates ratios from all currencies in JSON file.
It will show at the bottom of posts if all options are set and the post is older than one week, post category set to 'Currency' and tag set to 'EUR'.
Links to posts on 'Homepage'.
```<?php fr_show_in_post(get_the_ID()); ?>``` should be added to single.php in your theme folder.

### Version
1.0 alpha

### Bugs
There is a possibility of some bugs which will be fixed.
