<?php
namespace modules\teentixcsp;

use Craft;
use craft\web\View;
use yii\base\Event;

class Module extends \yii\base\Module
{
    private $csp = '';

    public function init()
    {
        Craft::setAlias('@modules/teentixcsp', __DIR__);

        if (Craft::$app->getRequest()->getIsConsoleRequest()) {
            $this->controllerNamespace = 'modules\\teentixcsp\\console\\controllers';
        } else {
            $this->controllerNamespace = 'modules\\teentixcsp\\controllers';
        }

        parent::init();

        $frameAncestors = ["'self'"];

        $baseCpUrl = getenv('BASE_CP_URL');
        if ($baseCpUrl !== false) {
            $frameAncestors[] = rtrim($baseCpUrl, '/');
        }

        $siteUrlLa = getenv('SITE_URL_LA');
        if ($siteUrlLa !== false) {
            $frameAncestors[] = rtrim($siteUrlLa, '/');
        }

        $siteUrlSea = getenv('SITE_URL_SEA');
        if ($siteUrlSea !== false) {
            $frameAncestors[] = rtrim($siteUrlSea, '/');
        }

        $siteUrl = getenv('SITE_URL');
        if ($siteUrl !== false) {
            $frameAncestors[] = rtrim($siteUrl, '/');
        }

        $frameAncestorsValue = implode(' ', $frameAncestors);
        $this->csp = "frame-ancestors $frameAncestorsValue;";

        Event::on(
            View::class,
            View::EVENT_END_PAGE,
            function(Event $event) {
                Craft::$app->response->headers->set('Content-Security-Policy', $this->csp);
            }
        );
    }
}

