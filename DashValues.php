<?php
/*
Dash Value for Mediawiki

The MIT License (MIT)

Copyright (c) 2015 Alexandre Devilliers

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
        'name'           => 'Dash Values',
        'version'        => '1.1.0',
        'author'         => 'Alexandre Devilliers',
        'descriptionmsg' => 'dashvalues-desc',
        'url'            => 'https://github.com/elbereth/DashValue',
);

$wgExtensionMessagesFiles['DashValues'] = dirname(__FILE__) . '/DashValues.i18n.php';

$wgHooks['ParserFirstCallInit'][] = 'wfDashValueParserInit';

$wgDashvaluesIncludes = __DIR__ . '/includes';

$wgAutoloadClasses['DashValues'] = $wgDashvaluesIncludes . '/DashValues.class.php';
$wgAutoloadClasses['EZCache'] = $wgDashvaluesIncludes . '/EZCache.class.php';
if (file_exists($wgDashvaluesIncludes . '/KrakenAPIClient.php')) {
	$wgAutoloadClasses['KrakenAPI'] = $wgDashvaluesIncludes . '/KrakenAPIClient.php';
	// If false do not use KrakenAPI (all EUR values not available)
	$wgDashValuesUseKrakenAPI = true;
}
else {
	$wgDashValuesUseKrakenAPI = false;
}

// Available values:
//   USD/DASH - From CryptoAPI (Average)
//   DASH/BTC - From CryptoAPI (Average)
//   EUR/BTC - From KrakenAPI
//   EUR/DASH - From CryptoAPI+KrakenAPI
$wgDashValuesDefault = 'USD/DASH';

// Locale to use to format decimals
$wgDashValuesFormatLocale = 'en_EN';

// Refresh cache every x seconds (is negative or 0 it will always fetch = SLOW)
$wgDashValuesFetchInterval = 3600;

// Hook our callback function into the parser
function wfDashValueParserInit( Parser $parser ) {

	global $wgOut;

	// When the parser sees the <sample> tag, it executes 
	// the wfSampleRender function (see below)
	$parser->setHook( 'dashvalue', 'wfDashValueRender' );

//        $parser->disableCache();
        $wgOut->enableClientCache(false);

        // Always return true from this function. The return value does not denote
        // success or otherwise have meaning - it just must always be true.
	return true;
}

// Execute
function wfDashValueRender( $input, array $args, Parser $parser, PPFrame $frame ) {

	global $wgDashValuesDefault, $wgDashValuesUseKrakenAPI, $wgDashValuesFormatLocale;

	$valuetype = $wgDashValuesDefault;

	// Retrieve parameters
	foreach( $args as $name => $value )
	{
		if ($name == 'value') {
			if (strcasecmp($value,'BTC/DASH') == 0) {
				$valuetype = 'BTC/DASH';
			}
			elseif (strcasecmp($value,'DASH/BTC') == 0) {
				$valuetype = 'DASH/BTC';
			}
			elseif (strcasecmp($value,'USD/DASH') == 0) {
				$valuetype = 'USD/DASH';
			}
                        elseif (strcasecmp($value,'EUR/DASH') == 0) {
                                $valuetype = 'EUR/DASH';
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

	$dvClass = new DashValues($wgDashValuesUseKrakenAPI,$wgDashValuesFormatLocale);

	return htmlspecialchars($dvClass->GetValue($valuetype));
}

?>
