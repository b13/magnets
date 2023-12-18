<?php

declare(strict_types=1);

namespace B13\Magnets\Command;

/*
 * This file is part of TYPO3 CMS-based project magnets by b13.
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
    protected ?SymfonyStyle $io;
    protected string $baseUrl = 'https://download.maxmind.com/app/geoip_download?suffix=tar.gz';
    protected array $remoteEditions = [
        'GeoLite2-City',
        'GeoLite2-Country',
    ];

    /**
     * Configure the command by defining the name, options and arguments
     */
    protected function configure()
    {
        $this->setDescription('Updates the MaxMind library files. Should be called at the beginning of each month recurring.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);
        $this->io->title($this->getDescription());

        if (empty($GLOBALS['TYPO3_CONF_VARS']['SYS']['GeoIPLicenceKey'])) {
            $this->io->error('Provide a licence key, see README.md');
            return self::FAILURE;
        }

        $targetPath = $GLOBALS['TYPO3_CONF_VARS']['SYS']['GeoIPPath'] . '/';
        if (!file_exists($targetPath)) {
            GeneralUtility::mkdir_deep($targetPath);
        }
        $this->baseUrl .= '&license_key=' . $GLOBALS['TYPO3_CONF_VARS']['SYS']['GeoIPLicenceKey'];
        $requestFactory = GeneralUtility::makeInstance(RequestFactory::class);
        foreach ($this->remoteEditions as $edition) {
            $url = $this->baseUrl . '&edition_id=' . $edition;
            $targetFile = $targetPath . $edition . '.tar.gz';
            $this->io->writeln('Fetching "' . $url . '" to ' . $targetFile);
            $response = $requestFactory->request($url);
            if ($response->getStatusCode() === 200) {
                GeneralUtility::writeFile($targetFile, $response->getBody()->getContents());
                $process = new Process(['tar', '--strip-components=1', '-xz', '--exclude=*txt', '-f', $targetFile], $targetPath);
                $process->run();
                unlink($targetFile);
            }
        }
        $this->io->success('Downloaded all files from MaxMind into ' . $targetPath);
        return self::SUCCESS;
    }
}
