Darkcoin Values v1.0.2
By Alexandre Devilliers (Made for darkcoin.fr and darkcoin.eu)

Retrieve values for BTC/DRK (via CryptoAPI on Poloniex, Cryptsy and C-Cex).
Can also retrieve value against EUR fiat (via Kraken)

Requirement:
* KrakenAPIClient.php from Kraken (https://github.com/payward/kraken-api-client)
* Disable cache on the pages where this is used, or it will not update until the cache expires.
  You can use MagicNoCache plugin for this purpose.
* apc or apcu php extensions (this will be dropped in future versions)

Install:
* Go to your MediaWiki extensions sub-folder (ex: cd /home/mediawiki/www/extensions/)
* Get Darkcoin Value from github:
  git clone https://github.com/elbereth/DarkcoinValue.git
* This should have created a folder DarkcoinValue in your extensions sub-folder
* Go to the Darkcoin Value include subfolder and get KrakenAPIClient.php:
  wget https://raw.githubusercontent.com/payward/kraken-api-client/master/php/KrakenAPIClient.php

Configuration:
* Add the following line at the end of your LocalSettings.php file:
  require_once("$IP/extensions/DarkcoinValues/DarkcoinValues.php" );
* Here are some parameters to setup:
  
// Available values:
//   USD/DRK - From CryptoAPI (Average)
//   DRK/BTC - From CryptoAPI (Average)
//   BTC/DRK - From CryptoAPI (Average)
//   EUR/BTC - From KrakenAPI
//   EUR/DRK - From CryptoAPI+KrakenAPI
//   LastRefresh - Indicate the last refresh date/time for values
$wgDarkcoinValuesDefault = 'USD/DRK';

// If false do not use KrakenAPI (all EUR values not available)
// Set to false if you don't use EUR values, it will speed up the queries
$wgDarkcoinValuesUseKrakenAPI = true;

// Locale to use to format decimals
$wgDarkcoinValuesFormatLocale = 'fr_FR';

// Refresh cache every x seconds (is negative or 0 it will always fetch = SLOW)
$wgDarkcoinValuesFetchInterval = 3600;

From your pages in the Wiki:
<darkcoinvalue value="BTC/DRK" />
<darkcoinvalue value="DRK/BTC" />
<darkcoinvalue value="USD/DRK" />
<darkcoinvalue value="EUR/BTC" />
<darkcoinvalue value="EUR/DRK" />
<darkcoinvalue value="LastRefresh" />


History:
v1.0.0 (2014-03-11)
* Initial working version (used on darkcoin.fr)

v1.0.2 (2014-04-02)
* Added caching via APC/APCU (also used as fallback when query failed)
