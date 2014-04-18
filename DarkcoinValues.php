<?php
/*
Darkcoin Value for Mediawiki

The MIT License (MIT)

Copyright (c) 2014 Alexandre Devilliers

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/

if ( !defined( 'MEDIAWIKI' ) ) {
        die( 'This file is a MediaWiki extension, it is not a valid entry point' );
}

$wgExtensionCredits['parserhook'][] = array(
        'path'           => __FILE__,
        'name'           => 'Darkcoin Values',
        'version'        => '1.0.5',
        'author'         => 'Alexandre Devilliers',
        'descriptionmsg' => 'darkcoinvalues-desc',
        'url'            => 'https://github.com/elbereth/DarkcoinValue',
);

$wgExtensionMessagesFiles['DarkcoinValues'] = dirname(__FILE__) . '/DarkcoinValues.i18n.php';

$wgHooks['ParserFirstCallInit'][] = 'wfDarkcoinValueParserInit';

$wgDarkcoinvaluesIncludes = __DIR__ . '/includes';

$wgAutoloadClasses['DarkcoinValues'] = $wgDarkcoinvaluesIncludes . '/DarkcoinValues.class.php';
$wgAutoloadClasses['EZCache'] = $wgDarkcoinvaluesIncludes . '/EZCache.class.php';
if (file_exists($wgDarkcoinvaluesIncludes . '/KrakenAPIClient.php')) {
	$wgAutoloadClasses['KrakenAPI'] = $wgDarkcoinvaluesIncludes . '/KrakenAPIClient.php';
	// If false do not use KrakenAPI (all EUR values not available)
	$wgDarkcoinValuesUseKrakenAPI = true;
}
else {
	$wgDarkcoinValuesUseKrakenAPI = false;
}

// Available values:
//   USD/DRK - From CryptoAPI (Average)
//   DRK/BTC - From CryptoAPI (Average)
//   EUR/BTC - From KrakenAPI
//   EUR/DRK - From CryptoAPI+KrakenAPI
$wgDarkcoinValuesDefault = 'USD/DRK';

// Locale to use to format decimals
$wgDarkcoinValuesFormatLocale = 'en_EN';

// Refresh cache every x seconds (is negative or 0 it will always fetch = SLOW)
$wgDarkcoinValuesFetchInterval = 3600;

// Hook our callback function into the parser
function wfDarkcoinValueParserInit( Parser $parser ) {

	global $wgOut;

	// When the parser sees the <sample> tag, it executes 
	// the wfSampleRender function (see below)
	$parser->setHook( 'darkcoinvalue', 'wfDarkcoinValueRender' );

//        $parser->disableCache();
        $wgOut->enableClientCache(false);

        // Always return true from this function. The return value does not denote
        // success or otherwise have meaning - it just must always be true.
	return true;
}

// Execute
function wfDarkcoinValueRender( $input, array $args, Parser $parser, PPFrame $frame ) {

	global $wgDarkcoinValuesDefault, $wgDarkcoinValuesUseKrakenAPI, $wgDarkcoinValuesFormatLocale;

	$valuetype = $wgDarkcoinValuesDefault;

	// Retrieve parameters
	foreach( $args as $name => $value )
	{
		if ($name == 'value') {
			if (strcasecmp($value,'BTC/DRK') == 0) {
				$valuetype = 'BTC/DRK';
			}
			elseif (strcasecmp($value,'DRK/BTC') == 0) {
				$valuetype = 'DRK/BTC';
			}
			elseif (strcasecmp($value,'USD/DRK') == 0) {
				$valuetype = 'USD/DRK';
			}
                        elseif (strcasecmp($value,'EUR/DRK') == 0) {
                                $valuetype = 'EUR/DRK';
                        }
                        elseif (strcasecmp($value,'EUR/BTC') == 0) {
                                $valuetype = 'EUR/BTC';
                        }
                        elseif (strcasecmp($value,'LastRefresh') == 0) {
                                $valuetype = 'LastRefresh';
                        }
                        elseif (substr($value,0,5) == 'DEBUG') {
                                $valuetype = $value;
                        }
		}
	}

	$dvClass = new DarkcoinValues($wgDarkcoinValuesUseKrakenAPI,$wgDarkcoinValuesFormatLocale);

	return htmlspecialchars($dvClass->GetValue($valuetype));
}

?>
