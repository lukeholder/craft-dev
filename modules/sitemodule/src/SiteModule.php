<?php
/**
 * Site module for Craft CMS 3.x
 *
 * An example module for Craft CMS 3 that lets you enhance your websites with a custom site module
 *
 * @link      https://nystudio107.com/
 * @copyright Copyright (c) 2018 nystudio107
 */

namespace modules\sitemodule;

use modules\sitemodule\assetbundles\sitemodule\SiteModuleAsset;
use modules\sitemodule\UserExporter;

use Craft;
use craft\elements\User;
use craft\events\RegisterElementExportersEvent;
use craft\events\RegisterTemplateRootsEvent;
use craft\events\TemplateEvent;
use craft\events\UserEvent;
use craft\i18n\PhpMessageSource;
use craft\web\View;

use craft\models\UserGroup;
use craft\elements\Entry;
use craft\helpers\App;
use craft\helpers\Template;
use craft\services\Users;
use craft\services\UserGroups;

use MailchimpMarketing;

use yii\base\Event;
use yii\base\InvalidConfigException;
use yii\base\Module;

/**
 * Class SiteModule
 *
 * @author    nystudio107
 * @package   SiteModule
 * @since     1.0.0
 *
 */
class SiteModule extends Module
{

  // ===========================================================================================
  // Static Properties
  // ===========================================================================================

  /**
   * @var SiteModule
   */
  public static $instance;

  // ===========================================================================================
  // CONSTRUCTOR
  // ===========================================================================================

  /**
   * @inheritdoc
   */
  public function __construct($id, $parent = null, array $config = [])
  {
    Craft::setAlias('@modules/sitemodule', $this->getBasePath());
    $this->controllerNamespace = 'modules\sitemodule\controllers';

    // -----------------------------------------------------------------------------------------
    // TRANSLATION CATEGORY
    // -----------------------------------------------------------------------------------------
    $i18n = Craft::$app->getI18n();
    /** @noinspection UnSafeIsSetOverArrayInspection */
    if (!isset($i18n->translations[$id]) && !isset($i18n->translations[$id.'*'])) {
      $i18n->translations[$id] = [
        'class' => PhpMessageSource::class,
        'sourceLanguage' => 'en-US',
        'basePath' => '@modules/sitemodule/translations',
        'forceTranslation' => true,
        'allowOverrides' => true,
      ];
    }

    // -----------------------------------------------------------------------------------------
    // BASE TEMPLATE DIRECTORY
    // -----------------------------------------------------------------------------------------
    Event::on(View::class, View::EVENT_REGISTER_CP_TEMPLATE_ROOTS, function (RegisterTemplateRootsEvent $e) {
        if (is_dir($baseDir = $this->getBasePath().DIRECTORY_SEPARATOR.'templates')) {
            $e->roots[$this->id] = $baseDir;
        }
    });

    // -----------------------------------------------------------------------------------------
    // SET AS THE GLOBAL INSTANCE OF THIS MODULE
    // -----------------------------------------------------------------------------------------
    static::setInstance($this);

    // -----------------------------------------------------------------------------------------
    // CALL THE PARENT CONSTRUCTOR
    // -----------------------------------------------------------------------------------------
    parent::__construct($id, $parent, $config);

  }

  // ===========================================================================================
  // INITIALIZER
  // ===========================================================================================

  /**
   * @inheritdoc
   */
  public function init()
  {

    // -----------------------------------------------------------------------------------------
    // CALL THE PARENT INITIALIZER
    // -----------------------------------------------------------------------------------------
    parent::init();
    self::$instance = $this;

    // -----------------------------------------------------------------------------------------
    // REGISTER THE ASSET BUNDLE FOR CONTROL PANEL CALLS
    // -----------------------------------------------------------------------------------------
    if (Craft::$app->getRequest()->getIsCpRequest()) {
      Event::on(
        View::class,
        View::EVENT_BEFORE_RENDER_TEMPLATE,
        function (TemplateEvent $event) {
          try {
            Craft::$app->getView()->registerAssetBundle(SiteModuleAsset::class);
          } catch (InvalidConfigException $e) {
            Craft::error(
              'Error registering AssetBundle - '.$e->getMessage(),
              __METHOD__
            );
          }
        }
      );
    }

    // -----------------------------------------------------------------------------------------
    // REGISTER CUSTOM EXPORTER TYPES
    // -----------------------------------------------------------------------------------------
    Event::on(
      User::class, 
      Entry::EVENT_REGISTER_EXPORTERS, 
      function(RegisterElementExportersEvent $e) {
        $e->exporters[] = UserExporter::class;
        $e->exporters[] = UserBasicExporter::class;
      }
    );

    // -----------------------------------------------------------------------------------------
    // ON REGISTRATION: CREATE PASS, SIGN-UP FOR NEWSLETTER
    // -----------------------------------------------------------------------------------------
    Event::on(
      Users::class,
      Users::EVENT_AFTER_ACTIVATE_USER,
      function (Event $event) {
        $user = $event->user;
        $userId = $user->id;
        $userEmail = $user->email;
        $userName = $user->fullName;
        $userSite = $user->userSite;
        
        if($userSite=="la") {
          $passSiteId = 2;
          $passSectionId = 16;
          $passTypeId = 23;
          $mailchimpApi = getenv('LA_MAILCHIMP_API');
          $mailchimpList = '128e90aeae';
        } else {
          $passSiteId = 1;
          $passSectionId = 11;
          $passTypeId = 12;
          $mailchimpApi = getenv('MAILCHIMP_API');
          $mailchimpList = 'c51b9e3d2f';
        }
        $haveMailchimpApiAndList = !empty($mailchimpApi) && !empty($mailchimpList);
        if ($haveMailchimpApiAndList) {
          $mailchimpApiParts = explode('-', $mailchimpApi, 2);
          if (count($mailchimpApiParts) == 2) {
            $mailchimpServer = $mailchimpApiParts[1];
          } else {
            $mailchimpServer = 'us6';
          }
        }
        
        //$currentUser = Craft::$app->users->getUserById($userId);   
        
        // create a digital pass
        $entry = new Entry();
        $entry->sectionId = $passSectionId;
        $entry->typeId = $passTypeId;
        $entry->siteId = $passSiteId;
        $entry->authorId = 306;
        $entry->enabled = true;
        $entry->title = "Digital Pass User - " . $user->id;
        $entry->setFieldValues([
          'passUser' => $user->id,
          'passStatus' => "assigned",
          'passFormat' => "digital",
        ]);
        $success = Craft::$app->elements->saveElement($entry);
        if (!$success) {
          Craft::error('Couldn’t save the entry "'.$entry->title.'"', __METHOD__);
        }

        // if selected, sign up user for mailchimp newsletter
        if ($haveMailchimpApiAndList && $user->userNewsletter == 1) {
          $mailchimp = new MailchimpMarketing\ApiClient();
          $mailchimp->setConfig([
            'apiKey' => $mailchimpApi,
            'server' => $mailchimpServer,
          ]);
          $subscriberHash = md5(strtolower($user->email));
          $response = $mailchimp->lists->setListMember($mailchimpList, $subscriberHash, [
            'email_address' => $user->email,
            'status_if_new' => 'subscribed',
          ]);
          if ($response->status !== 'subscribed') {
            Craft::warning('Could not subscribe to MailChimp list, error response: '.json_encode($response), 'application');
          }
        }
      }
    );
    
    // -----------------------------------------------------------------------------------------
    // ON REGISTRATION: SEND WELCOME EMAIL
    // -----------------------------------------------------------------------------------------
    Event::on(
      Users::class,
      Users::EVENT_AFTER_ASSIGN_USER_TO_GROUPS,
      function (Event $event) {
        $userId = $event->userId;
        $userGroup = $event->groupIds;
        
        // Send welcome email only if user is assigned to "Teen" group and no other groups
        if (in_array(3, $userGroup) && count($userGroup) == 1) {

          $user = Craft::$app->users->getUserById($userId);
          $userEmail = $user->email;
          $userName = $user->fullName;
          $userSite = $user->userSite;
          $userIsProvisional = $user->userIsProvisional;
          $dateCreated = $user->dateCreated;

          // send user activation email
          //$settings = App::mailSettings();
          //$adminEmail = Craft::parseEnv($settings->fromEmail);
          if ($userSite == "la") {
            $fromEmail = 'info@la.teentix.org';
          } else {
            $fromEmail = 'info@teentix.org';
          }
          
          // Use the site template mode  
          $view = Craft::$app->getView();
          $templateMode = $view->getTemplateMode();
          $view->setTemplateMode($view::TEMPLATE_MODE_SITE);
          $welcomeTextTemplate = '_emails/notifications/welcome-text';
          $welcomeHtmlTemplate = '_emails/notifications/welcome';
          
          // send welcome email
          Craft::$app->getMailer()->compose()
            ->setFrom($fromEmail)
            ->setSubject('Welcome to TeenTix!')
            ->setTextBody($view->renderTemplate($welcomeTextTemplate, array_merge( [
              'name' => $userName,
              'userId' => $userId,
              'userIsProvisional' => $userIsProvisional,
              'email' => $userEmail,
              'dateCreated' => $dateCreated,
            ])))
            ->setHtmlBody($view->renderTemplate($welcomeHtmlTemplate, array_merge( [
              'name' => $userName,
              'userId' => $userId,
              'userIsProvisional' => $userIsProvisional,
              'email' => $userEmail,
              'dateCreated' => $dateCreated,
            ])))
            ->setTo($userEmail)
            ->send();
        }
      }

    );

    // -----------------------------------------------------------------------------------------
    // MODULE METADATA
    // -----------------------------------------------------------------------------------------
    Craft::info(
      Craft::t(
        'site-module',
        '{name} module loaded',
        ['name' => 'Site']
      ),
        __METHOD__
    );
  }

}