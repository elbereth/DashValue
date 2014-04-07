# Darkcoin Values for MediaWiki
By Alexandre Devilliers (Made for darkcoin.fr and darkcoin.eu)
https://github.com/elbereth/DarkcoinValue

Retrieve values for BTC/DRK (via CryptoAPI on Poloniex, Cryptsy and C-Cex). Can also retrieve value against EUR fiat (via Kraken)

## Requirement:
* Disable cache on the pages where this is used, or it will not update until the cache expires. You can use MagicNoCache plugin for this purpose.
* apc or apcu php extensions (this will be dropped in future versions)
* intl extension (to format numbers)

## Optional:
* KrakenAPIClient.php from Kraken (https://github.com/payward/kraken-api-client) for EUR values

## Install:
* Go to your MediaWiki extensions sub-folder (ex: cd /home/mediawiki/www/extensions/)
* Get Darkcoin Value from github:

  git clone https://github.com/elbereth/DarkcoinValue.git

* This should have created a folder DarkcoinValue in your extensions sub-folder
* Optional: Go to the Darkcoin Value include subfolder and get KrakenAPIClient.php:

  wget https://raw.githubusercontent.com/payward/kraken-api-client/master/php/KrakenAPIClient.php

## Configuration:
* Add the following line at the end of your LocalSettings.php file:
```PHP
  require_once("$IP/extensions/DarkcoinValue/DarkcoinValues.php" );
```

* Here are some parameters to setup:

```PHP  
// Available values:
//   USD/DRK - From CryptoAPI (Average)
//   DRK/BTC - From CryptoAPI (Average)
//   BTC/DRK - From CryptoAPI (Average)
//   EUR/BTC - From KrakenAPI
//   EUR/DRK - From CryptoAPI+KrakenAPI
//   LastRefresh - Indicate the last refresh date/time for values
// [Default is USD/DRK]
$wgDarkcoinValuesDefault = 'USD/DRK';

// If false do not use KrakenAPI (all EUR values not available)
// Set to false if you don't use EUR values, it will speed up the queries
// [Default to False if KrakenAPIClient.php is not installed]
// [           True if installed]
$wgDarkcoinValuesUseKrakenAPI = false;

// Locale to use to format decimals
// [Default is en_EN for English]
$wgDarkcoinValuesFormatLocale = 'en_EN';

// Refresh cache every x seconds (is negative or 0 it will always fetch = SLOW)
// [Default is 3600]
$wgDarkcoinValuesFetchInterval = 3600;
```

From your pages in the Wiki:

```HTML
<darkcoinvalue value="BTC/DRK" />
<darkcoinvalue value="DRK/BTC" />
<darkcoinvalue value="USD/DRK" />
<darkcoinvalue value="EUR/BTC" />
<darkcoinvalue value="EUR/DRK" />
<darkcoinvalue value="LastRefresh" />
```

## History:

### v1.0.4 (2014-04-07)
* Updated readme for needed intl extension
* Added internationalization (for the extension description)
* Added URL in description (toward github)
* Value values are now case insensitive (usd/DRK will work for example)
* KrakenAPI is now optional (extension will work without includes/KrakenAPIClient.php)

### v1.0.3 (2014-04-07)
* Added much needed exception checking on KrakenAPI call

### v1.0.2 (2014-04-02)
* Added caching via APC/APCU (also used as fallback when query failed)

### v1.0.0 (2014-03-11)
* Initial working version (used on darkcoin.fr)
