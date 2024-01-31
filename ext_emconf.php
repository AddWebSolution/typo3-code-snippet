<?php

/**
 * Extension Manager/Repository config file for ext "so_typo3".
 */
$EM_CONF[$_EXTKEY] = [
    'title' => 'So Typo3',
    'description' => 'Solo Typo3 extension for Showing Shortcodes',
    'category' => 'templates',
    'constraints' => [
        'depends' => [
            'typo3' => '12.4.0-12.4.99',
            'fluid_styled_content' => '12.4.0-12.4.99',
            'rte_ckeditor' => '12.4.0-12.4.99',
        ],
        'conflicts' => [
        ],
    ],
    'autoload' => [
        'psr-4' => [
            'PlusItde\\SoTypo3\\' => 'Classes',
        ],
    ],
    'state' => 'stable',
    'uploadfolder' => 0,
    'createDirs' => '',
    'clearCacheOnLoad' => 1,
    'author' => 'Plus IT',
    'author_email' => 'taniya.ganguly@plus-it.de',
    'author_company' => 'plus-it.de',
    'version' => '1.0.0',
];
