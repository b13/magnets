<?php

declare(strict_types=1);

namespace B13\Magnets\ExpressionLanguage;

/*
 * This file is part of TYPO3 CMS-based extension "magnets" by b13.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

use B13\Magnets\IpLocation;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\ExpressionLanguage\AbstractProvider;
use TYPO3\CMS\Core\Http\NormalizedParams;

class GeoIpConditionProvider extends AbstractProvider
{
    public function __construct()
    {
        if (Environment::isCli()) {
            return;
        }

        $countryCode = '';
        $ipAddress = $this->getIpAddress();
        if ($ipAddress !== null) {
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

    protected function getIpAddress(): ?string
    {
        /** @var ?ServerRequestInterface $request */
        $request = $GLOBALS['TYPO3_REQUEST'] ?? null;
        if ($request === null) {
            return null;
        }
        /** @var ?NormalizedParams $normalizedParams */
        $normalizedParams = $request->getAttribute('normalizedParams');
        if ($normalizedParams === null) {
            return null;
        }
        return $normalizedParams->getRemoteAddress();
    }
}
