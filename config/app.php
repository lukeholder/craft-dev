<?php
/**
 * Yii Application Config
 *
 * Edit this file at your own risk!
 *
 * The array returned by this file will get merged with
 * vendor/craftcms/cms/src/config/app.php and app.[web|console].php, when
 * Craft's bootstrap script is defining the configuration for the entire
 * application.
 *
 * You can define custom modules and system components, and even override the
 * built-in system components.
 *
 * If you want to modify the application config for *only* web requests or
 * *only* console requests, create an app.web.php or app.console.php file in
 * your config/ folder, alongside this one.
 */

return [

  '*' => [
    'modules' => [
      'site-module' => [
            'class' => \modules\sitemodule\SiteModule::class,
      ],
        'teentixcsp' => [
            'class' => \modules\teentixcsp\Module::class,
        ],
    ],
    'bootstrap' => ['site-module', 'teentixcsp'],
  ],

  'dev' => [
    'components' => [
      'mailer' => function() {
        // Get the stored email settings
        $settings = \craft\helpers\App::mailSettings();
        $settings->transportType = \craft\mail\transportadapters\Smtp::class;
        $settings->transportSettings = [
          'useAuthentication' => true,
          'host' => 'smtp.mailtrap.io',
          'port' => '2525',
          'username' => getenv('MAILTRAP_USERNAME'),
          'password' => getenv('MAILTRAP_PASSWORD')
        ];
        
        $config = \craft\helpers\App::mailerConfig($settings);
        return Craft::createObject($config);
      },
    ],
  ],

  'staging' => [
  ],

  'production' => [
  ],
];
