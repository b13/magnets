# TYPO3 Extension "magnets"

This extension acts as a thin wrapper for TYPO3 v8, v9 and v10 to access GeoIP relevant information.

A symfony CLI Command can be added to download the latest GeoIP2 data.

For download the latest GeoIP2 data you have to provide an licence-key from [maxmind](https://www.maxmind.com/en/geolite2/signup)
(as .env-Variable or `$GLOBALS['TYPO3_CONF_VARS']['SYS']['GeoIPLicenceKey']`)

## Installation

Run `composer req b13/magnets` and install the extension via Extension Manager.

## Usage

Ensure your cronjob / scheduler task is running and use the IpLocation PHP class to have
a nice and quick API.

In addition, you have "countryCode" as TypoScript condition available.

    [countryCode == 'FR']
      page.10 = TEXT
      page.10.value = You are from france
    [global]

## License

Just as TYPO3 Core, this is an extension for TYPO3 and also licensed under GPL2+.

---


_Made by [b13](https://b13.com) with ♥_

[Find more TYPO3 extensions we have developed](https://b13.com/useful-typo3-extensions-from-b13-to-you) that help us deliver value in client projects. As part of the way we work, we focus on testing and best practices to ensure long-term performance, reliability, and results in all our code.


