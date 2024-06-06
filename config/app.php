<?php
use craft\helpers\App;
use testmodule\TestModule;

return [
    'id' => App::env('CRAFT_APP_ID') ?: 'CraftCMS',
    'modules' => ['test-module' => TestModule::class],
    'bootstrap' => ['test-module'],
];
