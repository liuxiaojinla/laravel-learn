<?php

return [
    'commands' => [
        \Plugins\craw\Commands\CrawContentCommand::class,
        \Plugins\craw\Commands\CrawMultiCommand::class,
        \Plugins\craw\Commands\CrawSiteCommand::class,
        \Plugins\craw\Commands\AnalysisContentCommand::class,
    ],
];
