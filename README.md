# TYPO3 Extension "magnets"

This extension acts as a thin wrapper for TYPO3 to access GeoIP relevant information.

A symfony CLI Command can be added to download the latest GeoIP2 data.

For download the latest GeoIP2 data you have to provide an licence-key from [maxmind](https://www.maxmind.com/en/geolite2/signup)
(as .env-Variable or `$GLOBALS['TYPO3_CONF_VARS']['SYS']['GeoIPLicenceKey']`)

Maxmind API requests are limited (1000 requests/day). As an alternative the databases
(GeoLite2-City, GeoLite2-Country) can be stores as a "Generic Package" in GitLab.

For Download during the CI run the `CI_JOB_TOKEN` can be used as follows:
```php
if (!empty(getenv('CI_JOB_TOKEN'))) {
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['GeoIPSource']['url'] = 'https://<GITLAB_HOST>/api/v4/projects/<PROJECT_ID>/packages/generic/GeoLite2/1.0.0/###REMOTE_EDITION###.tar.gz';
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['GeoIPSource']['headers'] = [
        'JOB-TOKEN' => getenv('CI_JOB_TOKEN'),
    ];
}
```

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

The condition is also available in site configurations.

## Store and Update databases using GitLab

Create a project and add a `.gitlab-ci.yml` 

```yaml
stages:
  - package-update

geo-ip:
  stage: package-update
  image: alpine/curl:8.9.1
  variables:
    GIT_STRATEGY: none
    GITLAB_RELEASE_VERSION: "1.0.0"
    PACKAGE_REGISTRY_URL: "${CI_API_V4_URL}/projects/${CI_PROJECT_ID}/packages/generic/GeoLite2"
  script:
    # Download Maxmind GeoIP database
    - mkdir -p downloads
    - curl -sSL "https://download.maxmind.com/app/geoip_download?edition_id=GeoLite2-Country&license_key=${GEOIP_LICENCE_KEY}&suffix=tar.gz" > downloads/GeoLite2-Country.tar.gz
    - curl -sSL "https://download.maxmind.com/app/geoip_download?edition_id=GeoLite2-City&license_key=${GEOIP_LICENCE_KEY}&suffix=tar.gz" > downloads/GeoLite2-City.tar.gz
    # Upload files to package
    - 'curl --fail-with-body --header "JOB-TOKEN: ${CI_JOB_TOKEN}" --upload-file ./downloads/GeoLite2-Country.tar.gz ${PACKAGE_REGISTRY_URL}/${GITLAB_RELEASE_VERSION}/GeoLite2-Country.tar.gz'
    - 'curl --fail-with-body --header "JOB-TOKEN: ${CI_JOB_TOKEN}" --upload-file ./downloads/GeoLite2-City.tar.gz ${PACKAGE_REGISTRY_URL}/${GITLAB_RELEASE_VERSION}/GeoLite2-City.tar.gz'
```

Under Settings -> CI/CD -> Variables add a variable named "GEOIP_LICENCE_KEY" containing
the licence-key.

For regular updates add a "New schedule" und Build -> Pipeline schedules.
e.g. `8 00 * * 1,3`

## License

Just as TYPO3 Core, this is an extension for TYPO3 and also licensed under GPL2+.

---


_Made by [b13](https://b13.com) with ♥_

[Find more TYPO3 extensions we have developed](https://b13.com/useful-typo3-extensions-from-b13-to-you) that help us deliver value in client projects. As part of the way we work, we focus on testing and best practices to ensure long-term performance, reliability, and results in all our code.


