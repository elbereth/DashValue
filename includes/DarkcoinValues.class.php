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

class DarkcoinValues {

    protected $useKraken;     // Should we use Kraken to retrieve EUR/BTC, if not we won't be able to give such value
    protected $KrakenAPI;     // Kraken instance
    protected $numFormat;
    protected $fetchInterval;
    protected $cache;

    function __construct($useKraken,$useLocale = 'en_EN',$fetchInterval = 3600)
    {

	// Use KrakenAPI for EUR conversion
	$this->useKraken = class_exists('KrakenAPI') && is_bool($useKraken) && $useKraken;
	if ($this->useKraken) {
		$this->KrakenAPI = new KrakenAPI('','');
	}

	// Use intl extension to format output
	if (extension_loaded('intl')) {
		$this->numFormat = new \NumberFormatter($useLocale, \NumberFormatter::DECIMAL);
		$this->numFormat->setAttribute(\NumberFormatter::MIN_FRACTION_DIGITS, 4); 
	        $this->numFormat->setAttribute(\NumberFormatter::MAX_FRACTION_DIGITS, 4);
	}

	// Fetch interval
	$this->fetchInterval = $fetchInterval;


	// Cache
	$this->cache = new EZCache();

    }

    function GetValue($value) {

        // By default output "???"
        $output = '???';
	$datadate = 0;
	$usedcache = false;

	$data1cached = $this->cache->retrieve('drkval1',$data1cachedsuccess);
	if (($data1cachedsuccess === TRUE) && isset($data1cached['datadate']) && isset($data1cached['data'])) {
		$data = $data1cached['data'];
		$datadate = $data1cached['datadate'];
		$usedcache = true;
	}

	if (($datadate + $this->fetchInterval) < time()) {
  	      // Retrieve values at cryptoapi (JSON)
  		$opts = array('http' =>
		  array(
		    'method'  => 'GET',
		    'timeout' => 2
		  )
		);
		$context = stream_context_create($opts);
	        $datatry = file_get_contents('http://drk.cryptoapi.net/',false, $context);

		// If UseKraken, retrieve EUR/BTC
		$euro2btc = false;
		if ($this->useKraken) {
			try {
	     			$dataKraken = $this->KrakenAPI->QueryPublic('Ticker', array('pair' => 'XBTCZEUR'));
				if (is_array($dataKraken) && isset($dataKraken['error']) && (count($dataKraken['error']) == 0)
				&& isset($dataKraken['result']) && is_array($dataKraken['result'])
				&& isset($dataKraken['result']['XXBTZEUR']) && is_array($dataKraken['result']['XXBTZEUR'])
				&& isset($dataKraken['result']['XXBTZEUR']['p']) && is_array($dataKraken['result']['XXBTZEUR']['p'])
				&& isset($dataKraken['result']['XXBTZEUR']['p'][1]) ) {
					$euro2btc = $dataKraken['result']['XXBTZEUR']['p'][1];
					$cancache = true;
				}
				else {
					$cancache = false;
 				}
			}
			catch (Exception $e) {
				// If call to Kraken API failed, fallback to cache
				if ($usedcache) {
					$euro2btc = $data['eurbtc'];
					$cancache = false;
				}
			}
		}
		else {
			$cancache = true;
		}

		if ($datatry !== FALSE) {
			$decoded = json_decode($datatry,true);
			if ($decoded != NULL) {
				$datadate = time();
				$usedcache = false;
			}
		}
		if ($usedcache) {
			$decoded = $data;
		}
                $decoded['eurbtc'] = $euro2btc;
	}
	else {
		$decoded = $data;
	}

        if (isset($decoded) && ($decoded != NULL)) {
		$drkvaluelist = array($decoded['drkavg'],$decoded['drk_btc_cryptsy'],$decoded['drk_btc_ccex'],$decoded['drk_btc_poloniex']);
		$curvalue = 0;
		$drkvalue = '???';
		while ((($drkvalue == '???') || ($drkvalue <= 0) || ($drkvalue == 1)) && $curvalue < count($drkvaluelist)) {
			$drkvalue = $drkvaluelist[$curvalue];
			$curvalue++;
		}
                if ($value == 'BTC/DRK') {
			$output = $drkvalue;
			if ($this->numFormat !== false) {
			        $this->numFormat->setAttribute(\NumberFormatter::MAX_FRACTION_DIGITS, 8);
				$output = $this->numFormat->format($output);
			}
	                $output = $output.' '.$value;
                }
                elseif ($value == 'DRK/BTC') {
                        $output = $drkvalue;
                        if ($output != '???') {
                          $output = 1 / $output;
                        }
                        if ($this->numFormat !== false) {
                                $output = $this->numFormat->format($output);
                        }
                        $output = $output.' '.$value;
                }
                elseif ($value == 'USD/DRK') {
                        $output = $decoded['drk_usd'];
                        if ($this->numFormat !== false) {
                                $output = $this->numFormat->format($output);
                        }
                        $output = $output.' '.$value;
                }
                elseif ($value == 'EUR/BTC') {
			if ($euro2btc !== false) {
 	                       $output = $decoded['eurbtc'];
        	                if ($this->numFormat !== false) {
                	                $output = $this->numFormat->format($output);
                        	}
	                        $output = $output.' '.$value;
			}
			else {
				$output = 'N/A';
			}
                }
                elseif ($value == 'EUR/DRK') {
                        if ($euro2btc !== false) {
 	                       $output = $drkvalue*$decoded['eurbtc'];
        	                if ($this->numFormat !== false) {
                	                $output = $this->numFormat->format($output);
                        	}
	                        $output = $output.' '.$value;
                        }
                        else {
                                $output = 'N/A';
                        }
                }
                elseif ($value == 'LastRefresh') {
                        $output = date('Y-m-d H:i:s',$datadate);
                }
                elseif ($value == 'DEBUG1') {
                        $output = print_r($data1cached,true);
                }

		if ((!$usedcache) && $cancache) {
			$data1tocache = array('data' => $decoded,
	                                     'datadate' => time());
			$this->cache->store('drkval1',$data1tocache);
		}
        }

	return $output;
 
    }
}

?>
