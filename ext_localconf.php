<?php
defined('TYPO3') or die('Access denied.');
/***************
 * Add default RTE configuration
 */
$GLOBALS['TYPO3_CONF_VARS']['RTE']['Presets']['so_typo3'] = 'EXT:so_typo3/Configuration/RTE/Default.yaml';
$GLOBALS['TYPO3_CONF_VARS']['FE']['cacheHash']['excludedParameters'][] = 'tx_short_code[page]';
$GLOBALS['TYPO3_CONF_VARS']['FE']['cacheHash']['excludedParameters'][] = 'tx_short_code[lang]';
$GLOBALS['TYPO3_CONF_VARS']['FE']['cacheHash']['excludedParameters'][] = 'tx_short_code[search]';
$GLOBALS['TYPO3_CONF_VARS']['FE']['cacheHash']['excludedParameters'][] = 'tx_short_code[id]';


/***************
 * PageTS
 */
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('<INCLUDE_TYPOSCRIPT: source="FILE:EXT:so_typo3/Configuration/TsConfig/Page/All.tsconfig">');

call_user_func(function () {
    $shortcodesExtConf = &$GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['so_typo3'];

    if (!is_array($shortcodesExtConf['processShortcode'] ?? false)) {
        $shortcodesExtConf['processShortcode'] = [];
    }

    $shortcodesExtConf['processShortcode'] = array_merge([
        'seminerList' => \PlusItde\SoTypo3\Keywords\SeminerListKeyword::class,
    ], $shortcodesExtConf['processShortcode']);
});
