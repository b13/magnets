<?php

declare(strict_types=1);

$EM_CONF[$_EXTKEY] = [
    'title' => 'Magnets - Its all attached',
    'description' => 'TYPO3 Service Package revolving around having a good API to fetch the current Geo IP and its location of the user.',
    'category' => 'fe',
    'state' => 'stable',
    'author' => 'b13.com',
    'author_email' => 'typo3@b13.com',
    'author_company' => 'b13 GmbH',
    'version' => '2.2.0',
    'constraints' => [
        'depends' => [
            'typo3' => '12.4.0-13.99.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
