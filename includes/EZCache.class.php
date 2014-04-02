<?php

define('EZCACHE_FILE',0);
define('EZCACHE_APC',1);

class EZCache {

    protected $cacheType;

    function __construct()
    {

	$this->cacheType = EZCACHE_FILE;

        // Use intl extension to format output
        if (extension_loaded('apc') || extension_loaded('apcu')) {
		$this->cacheType = EZCACHE_APC;
        }

    }


    function store($key, $var) {

	if ($this->cacheType == EZCACHE_APC) {
		return apc_store($key, $var);
	}
	else {
		
	}

    }


    function retrieve($key, &$success) {

        if ($this->cacheType == EZCACHE_APC) {
                return apc_fetch($key, $success);
        }
        else {

        }

    }

}

?>
