<?php
declare(strict_types=1);
namespace B13\Magnets\Command;

/*
 * This file is part of TYPO3 CMS-based project dbaudio by b13.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Process;
use TYPO3\CMS\Core\Http\RequestFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Updates the GeoIP public databsae from maxmind via Guzzle/curl
 * and unzips everything in the right place.
 * Should be a cronjob which runs usually on the first day of the month.
 */
class UpdateLibraryCommand extends Command
{

    /**
     * @var SymfonyStyle
     */
    protected $io;

    protected $baseUrl = 'https://geolite.maxmind.com/download/geoip/database/';

    protected $remoteFiles = [
        'dat' => [
            'GeoLiteCountry/GeoIP.dat.gz' => 'GeoIP.dat',
            'GeoIPv6.dat.gz' => 'GeoIPv6.dat',
            'GeoLiteCity.dat.gz' => 'GeoLiteCity.dat',
        ],
        'tar' => [
            'GeoLite2-City.tar.gz',
            'GeoLite2-Country.tar.gz',
        ]
    ];

    /**
     * Configure the command by defining the name, options and arguments
     */
    protected function configure()
    {
        $this->setDescription('Updates the MaxMind library files. Should be called at the beginning of each month recurring.');
    }

    /**
     * Executes the command for importing pages from previous installation
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);
        $this->io->title($this->getDescription());

        $targetPath = $GLOBALS['TYPO3_CONF_VARS']['SYS']['GeoIPPath'] . '/';
        if (!file_exists($targetPath)) {
            GeneralUtility::mkdir_deep($targetPath);
        }
        $requestFactory = GeneralUtility::makeInstance(RequestFactory::class);
        foreach ($this->remoteFiles as $type => $files) {
            if ($type === 'dat') {
                foreach ($files as $file => $targetFile) {
                    $url = $this->baseUrl . $file;
                    $targetFile = $targetPath . $targetFile;
                    $this->io->writeln('Fetching "' . $url . '" to ' . $targetFile);
                    $response = $requestFactory->request($url);
                    if ($response->getStatusCode() === 200) {
                        $content = $response->getBody()->getContents();
                        GeneralUtility::writeFile($targetFile, gzdecode($content));
                    }
                }
            }
            if ($type === 'tar') {
                foreach ($files as $file) {
                    $url = $this->baseUrl . $file;
                    $targetFile = $targetPath . $file;
                    $this->io->writeln('Fetching "' . $url . '" to ' . $targetFile);
                    $response = $requestFactory->request($url);
                    if ($response->getStatusCode() === 200) {
                        GeneralUtility::writeFile($targetFile, $response->getBody()->getContents());
                        $process = new Process('tar --strip-components=1 -xz --exclude=*txt -f ' . $targetFile, $targetPath);
                        $process->run();
                        unlink($targetFile);
                    }
                }
            }
        }
        $this->io->success('Downloaded all files from MaxMind into ' . $targetPath);
    }
}
