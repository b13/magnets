<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Magnets - Its all attached',
    'description' => 'TYPO3 Service Package revolving around having a good API to fetch the current Geo IP and its location of the user.',
    'category' => 'fe',
    'state' => 'stable',
    'author' => 'B13',
    'author_email' => 'typo3@b13.com',
    'author_company' => 'b13 GmbH',
    'version' => '2.1.1',
    'constraints' => [
        'depends' => [
            'typo3' => '11.5.0-12.99.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
