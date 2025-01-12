<?php

declare(strict_types=1);

namespace B13\Magnets\Command;

/*
 * This file is part of TYPO3 CMS-based extension "magnets" by b13.
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
 * Updates the GeoIP public database from maxmind via Guzzle
 * and unzips everything in the right place.
 * Should be a cronjob which runs usually on the first day of the month.
 */
class UpdateLibraryCommand extends Command
{
    protected ?SymfonyStyle $io;
    protected array $remoteEditions = [
        'GeoLite2-City',
        'GeoLite2-Country',
    ];

    protected function configure(): void
    {
        $this->setDescription('Updates the MaxMind library files. Should be called at the beginning of each month recurring.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $baseUrl = $GLOBALS['TYPO3_CONF_VARS']['SYS']['GeoIPSource']['url'];
        $this->io = new SymfonyStyle($input, $output);
        $this->io->title($this->getDescription());

        // Only maxmind requires a License key
        if (empty($GLOBALS['TYPO3_CONF_VARS']['SYS']['GeoIPLicenceKey']) && str_starts_with($baseUrl, 'https://download.maxmind.com')) {
            $this->io->error('Provide a licence key, see README.md');
            return self::FAILURE;
        }

        $targetPath = $GLOBALS['TYPO3_CONF_VARS']['SYS']['GeoIPPath'] . '/';
        if (!file_exists($targetPath)) {
            GeneralUtility::mkdir_deep($targetPath);
        }
        if(!empty($GLOBALS['TYPO3_CONF_VARS']['SYS']['GeoIPLicenceKey'])) {
            $baseUrl .= '&license_key=' . $GLOBALS['TYPO3_CONF_VARS']['SYS']['GeoIPLicenceKey'];
        }

        $requestFactory = GeneralUtility::makeInstance(RequestFactory::class);
        foreach ($this->remoteEditions as $edition) {
            $url = str_replace('###REMOTE_EDITION###', $edition, $baseUrl);
            $targetFile = $targetPath . $edition . '.tar.gz';
            $this->io->writeln('Fetching "' . $url . '" to ' . $targetFile);
            $request = $requestFactory->request($url, 'GET', [
                'headers' => $GLOBALS['TYPO3_CONF_VARS']['SYS']['GeoIPSource']['headers'],
            ]);

            if ($request->getStatusCode() === 200) {
                GeneralUtility::writeFile($targetFile, $request->getBody()->getContents());
                $process = new Process(['tar', '--strip-components=1', '-xz', '--exclude=*txt', '-f', $targetFile], $targetPath);
                $process->run();
                unlink($targetFile);
            }
        }
        $this->io->success('Downloaded all files from MaxMind into ' . $targetPath);
        return self::SUCCESS;
    }
}
