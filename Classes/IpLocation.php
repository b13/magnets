<?php

declare(strict_types=1);

namespace B13\Magnets;

/*
 * This file is part of TYPO3 CMS-based extension "magnets" by b13.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

use GeoIp2\Database\Reader;
use GeoIp2\Model\City;
use GeoIp2\Model\Country;

/**
 * Fetches Geo Location information from a given IP address
 * This should be later refactored to have different Backends / Adapters
 * to hook in php-module geoip, geoip2 or headers.
 */
class IpLocation
{
    protected string $ip;
    private City $cityRecord;
    private Country $countryRecord;

    public function __construct(string $ip)
    {
        // We could do some nice evaluation if the IP address is valid later-on
        $this->ip = $ip;
        $reader = new Reader($GLOBALS['TYPO3_CONF_VARS']['SYS']['GeoIPPath'] . '/GeoLite2-City.mmdb');
        $this->cityRecord = $reader->city($ip);
        $reader = new Reader($GLOBALS['TYPO3_CONF_VARS']['SYS']['GeoIPPath'] . '/GeoLite2-Country.mmdb');
        $this->countryRecord = $reader->country($ip);
    }

    public function getCityRecord(): City
    {
        return $this->cityRecord;
    }

    public function getCountryRecord(): Country
    {
        return $this->countryRecord;
    }

    /**
     * Get two-letter country code.
     */
    public function getCountryCode(): ?string
    {
        return $this->countryRecord->country->isoCode;
    }

    /**
     * Get two letter continent code
     */
    public function getContinentCode(): ?string
    {
        return $this->countryRecord->continent->code;
    }

    /**
     * Get location record.
     */
    public function getLocation(): array
    {
        return [
            'lat' => $this->cityRecord->location->latitude,
            'lng' => $this->cityRecord->location->longitude,
        ];
    }
}
