<?php

return [
	'frontend' => [
		'plus-itde/so_typo3/process-shortcodes' => [
			'target' => PlusItde\SoTypo3\Middleware\ProcessShortcodes::class,
			'before' => [
				'typo3/cms-frontend/output-compression',
			],
		],
	],
];
