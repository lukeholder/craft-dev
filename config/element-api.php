<?php

/*
CALENDAR IDS
  1: events
  2: eventsAdditional
  3: la_events
  4: la_eventsAdditional
*/

use craft\elements\Entry;
use craft\helpers\UrlHelper;
use craft\elements\Calendar_Events;
use League\Fractal\TransformerAbstract;

class TransformerCalendar extends TransformerAbstract
{
  public function transform(Solspace\Calendar\Elements\Event $event)
  {
    $calendarId = $event->calendar->id;
    $calendarHandle = $event->calendar->handle;
    $id = $event->id;
    $enabled = $event->enabled;
    $title = $event->title;
    $slug = $event->slug;
    $startDate = $event->startDate->format('Y-m-d');
    $startTime = $event->startDate->format('g:ia');
    $endDate = $event->endDate->format('Y-m-d');
    $endTime = $event->endDate->format('g:ia');
    $venue = $event->eventVenue;
    $partner = $event->eventPartner;

    if ($calendarHandle == "events" || $calendarHandle == "la_events") {
      $genre = $event->eventType;
      $category = $event->eventCategory;

      return [
        //'event' => $event,
        'calendar' => $calendarId,
        'id' => $id,
        'enabled' => $enabled,
        'title' => $title,
        'slug' => $slug,
        'start' => $event->startDate->format(\DateTime::ATOM),
        'end' => $event->endDate->format(\DateTime::ATOM),
        'startDate' => $startDate,
        'startTime' => $startTime,
        'endDate' => $endDate,
        'endTime' => $endTime,
        'allDay' => $event->allDay,
        'repeating' => $event->repeats(),
        'repeatingHumanReadable' => $event->getHumanReadableRepeatsString(),
        'repeatingSimple' => $event->getSimplifiedRepeatRule(),
        'venue'  => array_map(function(craft\elements\entry $venue) {
          return [
            'id' => $venue->id,
            'title' => $venue->title,
            'url' => $venue->url,
            'address' => $venue->partnerAddress ? $venue->partnerAddress->address : null,
          ];
        }, $venue->all()),
        'partner'  => array_map(function(craft\elements\entry $partner) {
          return [
            'id' => $partner->id,
            'title' => $partner->title,
            'url' => $partner->url,
          ];
        }, $partner->all()),
        'genre'  => array_map(function(craft\elements\category $genre) {
          return [
            'id' => $genre->id,
            'title' => $genre->title,
            'slug' => $genre->slug,
          ];
        }, $genre->all()),
        'category'  => array_map(function(craft\elements\category $cat) {
          return [
            'id' => $cat->id,
            'title' => $cat->title,
            'slug' => $cat->slug,
          ];
        }, $category->all()),
      ];
    }

    if ($calendarHandle == "eventsAdditional" || $calendarHandle == "la_eventsAdditional") {
      $parent = $event->eventRelated->one();
      
      if ($parent) {
        $id = $parent->id;
        $enabled = $parent->enabled;
        $title = $parent->title;
        $slug = $parent->slug;
        $venue = $parent->eventVenue;
        $partner = $parent->eventPartner;
        $genre = $parent->eventType;
        $category = $parent->eventCategory;

        return [
          //'event' => $event,
          'calendar' => $calendarId,
          'id' => $id,
          'enabled' => $enabled,
          'title' => $title,
          'slug' => $slug,
          'start' => $event->startDate->format(\DateTime::ATOM),
          'end' => $event->endDate->format(\DateTime::ATOM),
          'startDate' => $startDate,
          'startTime' => $startTime,
          'endDate' => $endDate,
          'endTime' => $endTime,
          'allDay' => $event->allDay,
          'repeating' => $event->repeats(),
          'repeatingHumanReadable' => $event->getHumanReadableRepeatsString(),
          'repeatingSimple' => $event->getSimplifiedRepeatRule(),
          'venue'  => array_map(function(craft\elements\entry $venue) {
            return [
              'id' => $venue->id,
              'title' => $venue->title,
              'url' => $venue->url,
              'address' => $venue->partnerAddress ? $venue->partnerAddress->address : null,
            ];
          }, $venue->all()),
          'partner'  => array_map(function(craft\elements\entry $partner) {
            return [
              'id' => $partner->id,
              'title' => $partner->title,
              'url' => $partner->url,
            ];
          }, $partner->all()),
          'genre'  => array_map(function(craft\elements\category $genre) {
            return [
              'id' => $genre->id,
              'title' => $genre->title,
              'slug' => $genre->slug,
            ];
          }, $genre->all()),
          'category'  => array_map(function(craft\elements\category $cat) {
            return [
              'id' => $cat->id,
              'title' => $cat->title,
              'slug' => $cat->slug,
            ];
          }, $category->all()),
        ];
      } else {
        return [
          'calendar' => $calendarId,
          'id' => $id,
          'enabled' => '0',
          'title' => $title,
          'slug' => $slug,
          'start' => $event->startDate->format(\DateTime::ATOM),
          'end' => $event->endDate->format(\DateTime::ATOM),
          'startDate' => $startDate,
          'startTime' => $startTime,
          'endDate' => $endDate,
          'endTime' => $endTime,
          'allDay' => $event->allDay,
          'repeating' => $event->repeats(),
          'repeatingHumanReadable' => $event->getHumanReadableRepeatsString(),
          'repeatingSimple' => $event->getSimplifiedRepeatRule(),
          'venue'  => [],
          'partner'  => [],
          'genre'  => [],
          'category'  => [],
        ];
      }
    }
  }
}


// TODO: test this locally!
function _resolve_cors_headers() {
    // https://ionicframework.com/docs/faq/cors#solutions-for-cors-errors
    $tt_ionic_local_dev = "http://localhost:8100";
    $tt_ionic_local_dev_android = "http://localhost:8080";
    $tt_ionic_android = "http://localhost";

    // FIXME: update this to support new domain layout
    switch (getenv('ENVIRONMENT')) {
        case "dev":
            $tt_cors_allowed_origins = ["*"];
            break;
        case "staging":
            $tt_cors_allowed_origins = [
                "https://staging.teentix.org",
                $tt_ionic_android,
                $tt_ionic_local_dev,
                $tt_ionic_local_dev_android
            ];
            break;
        case "production":
            $tt_cors_allowed_origins = [
                "https://www.teentix.org",
                $tt_ionic_android,
                $tt_ionic_local_dev,
                $tt_ionic_local_dev_android
            ];
            break;
        default:
            $tt_cors_allowed_origins = [];
    }

    $origin = Craft::$app->getRequest()->headers->get('Origin');
    if (in_array($origin, $tt_cors_allowed_origins) || in_array('*', $tt_cors_allowed_origins)) {
        Craft::$app->getResponse()->getHeaders()->set('Access-Control-Allow-Origin', $origin);
        Craft::$app->getResponse()->getHeaders()->set('Access-Control-Allow-Headers', 'Content-Type');
        Craft::$app->getResponse()->getHeaders()->set('Access-Control-Allow-Methods', 'GET, OPTIONS');
    }
}


return [
  'endpoints' => [
    'api/events.json' => function() {
      $site = Craft::$app->request->getParam('site') ?? 'default';
      $startDate = Craft::$app->request->getParam('start') ?? null;
      $endDate = Craft::$app->request->getParam('end') ?? Craft::$app->request->getParam('start');
      if($site == "la") {
        $calendarId = [3,4];
      } else {
        $calendarId = [1,2];
      }

      _resolve_cors_headers();
      return [
        'elementType' => 'Solspace\Calendar\Elements\Event',
        'criteria' => [
          'calendarId' => $calendarId,
          'rangeStart' => $startDate,
          'rangeEnd' => $endDate,
        ],
        'transformer' => new TransformerCalendar(),
        'elementsPerPage' => 1000,
        //'pretty' => true,
        'cache' => 'PT60M',
      ];
    },
    'api/events-date.json' => function() {
      $site = Craft::$app->request->getParam('site') ?? 'default';
      $date = Craft::$app->request->getParam('date') ?? 'today';
      $startDate = $date;
      $endDate = $date;
      if($site == "la") {
        $calendarId = [3,4];
      } else {
        $calendarId = [1,2];
      }

      _resolve_cors_headers();
      return [
        'elementType' => 'Solspace\Calendar\Elements\Event',
        'criteria' => [
          'calendarId' => $calendarId,
          'rangeStart' => $startDate,
          'rangeEnd' => $endDate,
          'orderBy' => 'startDate ASC'
        ],
        'transformer' => new TransformerCalendar(),
        'elementsPerPage' => 1000,
        //'pretty' => true,
        'cache' => 'PT24H',
      ];
    },
    'api/events-filtered.json' => function() {
      $site = Craft::$app->request->getQueryParam('site') ?? 'default';
      $date = Craft::$app->request->getQueryParam('date') ?? 'today';
      $isFree = filter_var(Craft::$app->request->getQueryParam('isFree'), FILTER_VALIDATE_BOOLEAN) ?? false;
      $genre = Craft::$app->request->getQueryParam('genre') ?? null;
      $cat = Craft::$app->request->getQueryParam('cat') ?? null;
      $partner = Craft::$app->request->getQueryParam('partner') ?? null;
      $venue = Craft::$app->request->getQueryParam('venue') ?? null;
      $freeCat = "";
      if($site == "la") {
        $calendarId = [3,4];
      } else {
        $calendarId = [1,2];
      }
      if($isFree) {
        $freeCat = "63";
      }

      if (str_contains($genre, "x")) {
        $myInterests = "or" . $genre;
        $myInterestsQuery = explode("x", $myInterests);
        $relatedQuery = array_filter(['and', $cat, $freeCat, $partner, $venue]);
        $relatedQuery = array_merge($relatedQuery, [$myInterestsQuery]);
      } else {
        $relatedQuery = array_filter(['and', $genre, $cat, $freeCat, $partner, $venue]);
      }
      
      #dd($relatedQuery);

      _resolve_cors_headers();
      return [
        'elementType' => 'Solspace\Calendar\Elements\Event',
        'criteria' => [
          'calendarId' => $calendarId,
          'rangeStart' => $date,
          'rangeEnd' => $date,
          'relatedTo' => $relatedQuery,
          'loadOccurrences' => true,
          'orderBy' => 'startDate ASC'
        ],
        'transformer' => new TransformerCalendar(),
        'elementsPerPage' => 1000,
        //'pretty' => true,
        'cache' => false,
      ];
    },
    'api/event/<entryId:\d+>.json' => function($entryId) {
      _resolve_cors_headers();
      return [
        'elementType' => 'Solspace\Calendar\Elements\Event',
        'criteria' => [ 'id' => $entryId ],
        'one' => true,
        'transformer' => new TransformerCalendar(),
        //'pretty' => true,
        'cache' => 'PT60M',
      ];
    },
    'api/passes.json' => function() {
      $status = Craft::$app->request->getParam('status') ?? null;
      $format = Craft::$app->request->getParam('format') ?? null;
      $q = Craft::$app->request->getParam('q') ?? null;
      _resolve_cors_headers();
      return [
        'elementType' => 'craft\elements\Entry',
        'criteria' => [
          'section' => 'passes',
          'passStatus' => $status,
          'passFormat' => $format,
          'search' => $q,
        ],
        'transformer' => function(Entry $entry) {
          $userid = $entry->passUser;
          $query = craft\elements\User::find();
          $query->id($userid);
          $user = $query->one();

          return [
            'id' => $entry->id,
            'status' => $entry->passStatus,
            'format' => $entry->passFormat,
            'userid' => $userid,
            'username' => $userid ? $user->name : null,
          ];
        },
        'elementsPerPage' => 100,
        //'pretty' => true,
        'cache' => 'PT60M',
      ];
    },
  ]
];