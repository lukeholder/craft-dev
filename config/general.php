<?php
/**
 * General Configuration
 *
 * All of your system's general configuration settings go in here. You can see a
 * list of the available settings in vendor/craftcms/cms/src/config/GeneralConfig.php.
 *
 * @see craft\config\GeneralConfig
 */



return [
    // Global settings
    '*' => [
        'allowUpdates' => false,
        'allowAdminChanges' => false,
        'allowedGraphqlOrigins' => false,  // this disables using graphql from web https://craftcms.com/docs/3.x/config/config-settings.html#allowedgraphqlorigins
        'backupOnUpdate' => false,
        'devMode' => false,
        'enableTemplateCaching' => true,
        'isSystemLive' => true,
        'baseCpUrl' => getenv('BASE_CP_URL'),
        'resourceBaseUrl' => '@cp/cpresources',
        
        'autoLoginAfterAccountActivation' => true,
        'activateAccountSuccessPath' => 'account',
        'cpTrigger' => 'admin',
        'defaultWeekStartDay' => 0,
        'deferPublicRegistrationPassword' => false,
        'enableCsrfProtection' => true,
        'loginPath' => 'account/login',
        'omitScriptNameInUrls' => true,
        'postLoginRedirect' => 'account',
        'setPasswordPath' => 'account/setpassword',
        'useEmailAsUsername' => true,
        'sendPoweredByHeader' => false,
        'runQueueAutomatically' => false,
    ],

    // Dev environment settings
    'dev' => [
        'aliases' => [
            '@assetsUrl' => getenv('ASSETS_URL') ?? 'https://dev.teentix.org/',
            '@web' => getenv('SITE_URL') ?? 'https://dev.teentix.org/',
            '@webLa' => getenv('SITE_URL_LA') ?? 'https://dev-la.teentix.org/',
            '@cp' => getenv('BASE_CP_URL') ?? 'https://dev-admin.teentix.org/',
        ],
        'allowedGraphqlOrigins' => [
            getenv('SITE_URL') ?? 'https://dev.teentix.org/',
            getenv('SITE_URL_SEA') ?? 'https://dev-seattle.teentix.org/',
            getenv('SITE_URL_LA') ?? 'https://dev-la.teentix.org/',
            getenv('BASE_CP_URL') ?? 'https://dev-admin.teentix.org/',
        ],
        'devMode' => true,
        'enableTemplateCaching' => false,
    ],

    // Staging environment settings
    'staging' => [
        'aliases' => [
            '@assetsUrl' => 'https://staging.teentix.org/',
            '@web' => 'https://staging.teentix.org/',
            '@webLa' => 'https://staging-la.teentix.org/',
            '@cp' => 'https://staging-admin.teentix.org/',
        ],
        'allowedGraphqlOrigins' => [
            'https://staging.teentix.org/',
            'https://staging-seattle.teentix.org/',
            'https://staging-la.teentix.org/',
            'https://staging-admin.teentix.org/',
        ],
        'devMode' => true,
        'enableTemplateCaching' => false,
    ],

    // Production environment settings
    'production' => [
        'aliases' => [
            '@assetsUrl' => 'https://www.teentix.org/',
            '@web' => 'https://www.teentix.org/',
            '@webLa' => 'https://la.teentix.org/',
            '@cp' => 'https://admin.teentix.org/',
        ],
        'allowedGraphqlOrigins' => [
            'https://www.teentix.org/',
            'https://seattle.teentix.org/',
            'https://la.teentix.org/',
            'https://admin.teentix.org/',
        ],
    ],
];
