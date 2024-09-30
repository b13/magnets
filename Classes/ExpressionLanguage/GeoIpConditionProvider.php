<?php
declare(strict_types = 1);

namespace B13\Magnets\ExpressionLanguage;

/*
 * This file is part of TYPO3 CMS-based extension "magnets" by b13.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

use B13\Magnets\IpLocation;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\ExpressionLanguage\AbstractProvider;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class GeoIpConditionProvider extends AbstractProvider
{
    public function __construct()
    {
        if (Environment::isCli()) {
            return;
        }

        $countryCode = '';
        $ipAddress = GeneralUtility::getIndpEnv('REMOTE_ADDR');
        if ($ipAddress) {
            try {
                $location = new IpLocation($ipAddress);
                $countryCode = $location->getCountryCode() ?? 'INVALID';
            } catch (\GeoIp2\Exception\AddressNotFoundException|\InvalidArgumentException $e) {
                $countryCode = 'INVALID';
            }
        }

        // We make the countryCode available in conditions for site configs base variants
        // to enable base URL switches depending on the country code.
        // e.g. countryCode == 'DK'
        $this->expressionLanguageVariables = [
            'countryCode' => strtoupper($countryCode),
        ];
    }
}
