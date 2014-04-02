<?php
if ( !defined( 'MEDIAWIKI' ) ) {
        die( 'This file is a MediaWiki extension, it is not a valid entry point' );
}

class DarkcoinValues {

    protected $useKraken;     // Should we use Kraken to retrieve EUR/BTC, if not we won't be able to give such value
    protected $KrakenAPI;     // Kraken instance
    protected $numFormat;
    protected $fetchInterval;
    protected $cache;

    function __construct($useKraken,$useLocale = 'fr_FR',$fetchInterval = 60)
    {

	// Use KrakenAPI for EUR conversion
	$this->useKraken = is_bool($useKraken) && $useKraken;
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
		else {
			$cancache = true;
		}

		if ($datatry !== FALSE) {
			$decoded = json_decode($datatry,true);
			if ($decoded != NULL) {
				$decoded['eurbtc'] = $euro2btc;
				$datadate = time();
				$usedcache = false;
			}
		}
	}
	else {
		$decoded = $data;
	}

        if ($decoded != NULL) {
                if ($value == 'BTC/DRK') {
			$output = $decoded['drkavg'];
			if ($this->numFormat !== false) {
			        $this->numFormat->setAttribute(\NumberFormatter::MAX_FRACTION_DIGITS, 8);
				$output = $this->numFormat->format($output);
			}
	                $output = $output.' '.$value;
                }
                elseif ($value == 'DRK/BTC') {
                        $output = $decoded['drkavg'];
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
 	                       $output = $decoded['drkavg']*$decoded['eurbtc'];
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

//        $output = $output.' - '.($time2-$time1).' ms - '.($time3-$time2).' ms - '.($time4-$time3).' ms = '.($time4-$time1).' ms';
	return $output;
 
    }
}

?>