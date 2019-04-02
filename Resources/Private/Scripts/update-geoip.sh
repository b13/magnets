#!/bin/bash
# Default TARGET_DIR=/usr/share/GeoIP
# otherwise php.ini settings need to be adjusted
# see http://php.net/manual/de/geoip.configuration.php
#
# What you need
# curl
# gunzip
# GNU tar
#
# Call it like
# "./update-geoip.sh /usr/share/GeoIP"
# and do this once a month

TARGET_DIR=$1

# Create the target directory if not existent
mkdir -p $TARGET_DIR
# change to target directory, then download all files
cd $TARGET_DIR
curl -o - http://geolite.maxmind.com/download/geoip/database/GeoLiteCountry/GeoIP.dat.gz | gunzip > GeoIP.dat
curl -o - http://geolite.maxmind.com/download/geoip/database/GeoIPv6.dat.gz | gunzip > GeoIPv6.dat
curl -o - http://geolite.maxmind.com/download/geoip/database/GeoLiteCity.dat.gz | gunzip > GeoLiteCity.dat
curl http://geolite.maxmind.com/download/geoip/database/GeoLite2-City.tar.gz | tar --strip-components=1 -xz --exclude=*txt
curl http://geolite.maxmind.com/download/geoip/database/GeoLite2-Country.tar.gz | tar --strip-components=1 -xz --exclude=*txt
