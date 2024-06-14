<?php
/**
 * Site URL Rules
 *
 * You can define custom site URL rules here, which Craft will check in addition
 * to any routes you’ve defined in Settings → Routes.
 *
 * See http://www.yiiframework.com/doc-2.0/guide-runtime-routing.html for more
 * info about URL rules.
 *
 * In addition to Yii’s supported syntaxes, Craft supports a shortcut syntax for
 * defining template routes:
 *
 *     'blog/archive/<year:\d{4}>' => ['template' => 'blog/_archive'],
 *
 * That example would match URIs such as `/blog/archive/2012`, and pass the
 * request along to the `blog/_archive` template, providing it a `year` variable
 * set to the value `2012`.
 */

return [
  'graphql' => 'graphql/api',
  'sign-up' => ['template' => 'account/applicant-type'],
  'account/new/?<applicant:\w+>?' => ['template' => 'account/new'],
  'account/pass/?<userid:\d+>' => ['template' => 'account/pass'],
  'blog/category/?<category:{slug}>?' => ['template' => 'blog/category'],
  'blog/author/?<author:\d+>?' => ['template' => 'blog/author'],
  'passes/user/?<userid:\d+>?' => ['template' => 'passes/user'],
  'calendar/event/<eventSlug:{slug}>' => ['template' => 'calendar/event'],
  'calendar/event/<eventSlug:{slug}>/add' => ['template' => 'calendar/ical'],
  'calendar/event/<eventSlug:{slug}>/checkin' => ['template' => 'checkin/event'],
  'calendar/month/<year:\d{4}>/<month:\d{2}>' => ['template' => 'calendar/month'],
  //'calendar/week/<year:\d{4}>/<month:\d{2}>/<day:\d{2}>' => ['template' => 'calendar/week'],
  'calendar/day/<year:\d{4}>/<month:\d{2}>/<day:\d{2}>' => ['template' => 'calendar/day'],
  'venues/<venueSlug:{slug}>/now' => ['template' => 'calendar/now'],
  'venues/<venueSlug:{slug}>/qrcode' => ['template' => 'venues/qrcode'],
  'checkin/pass/?<passId:\d+>?' => ['template' => 'checkin/pass'],
];
