<?php
defined('TYPO3_MODE') or die();

// Define the path to the MaxMind DB files
if (!isset($GLOBALS['TYPO3_CONF_VARS']['SYS']['GeoIPPath'])) {
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['GeoIPPath'] = getenv('TYPO3_PATH_APP')
        ? getenv('TYPO3_PATH_APP') . '/var/geoip'
        : '/usr/share/GeoIP';
}
