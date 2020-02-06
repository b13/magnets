# TYPO3 Extension "magnets"

This extension acts as a thin wrapper for TYPO3 v8/v9 to access GeoIP relevant information.

A symfony CLI Command can be added to download the latest GeoIP2 data.

For download the latest GeoIP2 data you have to provide an licence-key from [maxmind](https://www.maxmind.com/en/geolite2/signup)
(as .env-Variable or `$GLOBALS['TYPO3_CONF_VARS']['SYS']['GeoIPLicenceKey']`)

## Installation

Run `composer req b13/magnets` and install the extension via Extension Manager.

## License

Just as TYPO3 Core, this is an extension for TYPO3 and also licensed under GPL2+.

## Maintainers

Initially created by Benni Mack, maintained by awesome people within [b13 GmbH](https://b13.com), Germany, 2018-2019.


