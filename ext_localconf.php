<?php

defined('TYPO3') or die();

// Define the path to the MaxMind DB files
if (!isset($GLOBALS['TYPO3_CONF_VARS']['SYS']['GeoIPPath'])) {
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['GeoIPPath'] = getenv('TYPO3_PATH_APP')
        ? getenv('TYPO3_PATH_APP') . '/var/geoip'
        : '/usr/share/GeoIP';
}

// Define the licence key to access the MaxMind DB files
if (!isset($GLOBALS['TYPO3_CONF_VARS']['SYS']['GeoIPLicenceKey'])) {
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['GeoIPLicenceKey'] = getenv('MAGNETS_GEO_IP_LICENCE_KEY');
}

if (!isset($GLOBALS['TYPO3_CONF_VARS']['SYS']['GeoIPSource'])) {
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['GeoIPSource'] = [];
}

if (!isset($GLOBALS['TYPO3_CONF_VARS']['SYS']['GeoIPSource']['url'])) {
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['GeoIPSource']['url'] = 'https://download.maxmind.com/app/geoip_download?suffix=tar.gz&edition_id=###REMOTE_EDITION###';
}

if (!isset($GLOBALS['TYPO3_CONF_VARS']['SYS']['GeoIPSource']['headers'])) {
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['GeoIPSource']['headers'] = [];
}
