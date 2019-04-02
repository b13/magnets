<?php
declare(strict_types=1);
namespace B13\Magnets;

/*
 * This file is part of TYPO3 CMS-based project dbaudio by b13.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

use GeoIp2\Database\Reader;

/**
 * Fetches Geo Location information from a given IP address
 * This should be later refactored to have different Backends / Adapters
 * to hook in php-module geoip, geoip2 or headers.
 */
class IpLocation
{
    /**
     * @var string IP address
     */
    protected $ip;

    /**
     * @var \GeoIp2\Model\City
     */
    private $cityRecord;

    /**
     * @var \GeoIp2\Model\Country
     */
    private $countryRecord;

    /**
     * IpLocation constructor.
     * @param string $ip
     */
    public function __construct(string $ip)
    {
        // We could do some nice evaluation if the IP address is valid later-on
        $this->ip = $ip;
        $reader = new Reader($GLOBALS['TYPO3_CONF_VARS']['SYS']['GeoIPPath'] . '/GeoLite2-City.mmdb');
        $this->cityRecord = $reader->city($ip);
        $reader = new Reader($GLOBALS['TYPO3_CONF_VARS']['SYS']['GeoIPPath'] . '/GeoLite2-Country.mmdb');
        $this->countryRecord = $reader->country($ip);
    }

    /**
     * Get two letter country code.
     *
     *
     * @return string|false Country code or FALSE on failure
     */
    public function getCountryCode()
    {
        return $this->countryRecord->country->isoCode;
    }

    /**
     * Get location record.
     *
     * @return array|false Location data or FALSE on failure
     */
    public function getLocation()
    {
        return [
            'lat' => $this->cityRecord->location->latitude,
            'lng' => $this->cityRecord->location->longitude
        ];
    }
}
