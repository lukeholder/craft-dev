<?php

/**
 * UserExporter
 * 
 * Provides a custom exporter for user elements, only exporting the fields that are actually needed.
 */

namespace modules\sitemodule;

use craft\base\ElementExporter;
use craft\elements\User;
use craft\elements\db\ElementQueryInterface;
use craft\helpers\ArrayHelper;

class UserExporter extends ElementExporter {

    public static function displayName(): string {
    return 'User data';
  }

  public function export(ElementQueryInterface $query): array {

    $results = [];

    foreach ($query->each() as $user) {
      $results[] = [
        'id' => $user->id ?? '',
        'dateCreated' => $user->dateCreated?->format("M-d-Y") ?? '',
        'dateUpdated' => $user->dateUpdated?->format("M-d-Y") ?? '',
        'suspended' => $user->suspended ?? '',
        'site' => $user->userSite ?? '',
        'firstName' => $user->firstName ?? '',
        'lastName' => $user->lastName ?? '',
        'birthdate' => $user->userBirthdate?->format("M-d-Y") ?? '',
        'email' => $user->email ?? '',
        'phone' => $user->userPhone ?? '',
        'city' => $user->userCity ?? '',
        'state' => $user->userState ?? '',
        'zipcode' => $user->userZipcode ?? '',
      //'country' => $user->userCountry?->value ?? '',
        'school' => $user->userSchool ?? '',
        'newsletter' => $user->userNewsletter ?? '',
        'ethnicity' => ArrayHelper::getColumn(
          $user->userDemoEthnicity,
          'value'
        ),
        'gender' => $user->userDemoGender?->value ?? '',
        'genderOther' => $user->userDemoGenderOther ?? '',
        'languages' => ArrayHelper::getColumn(
          $user->userDemoLanguage,
          'value'
        ),
        'languageOther' => $user->userDemoLanguageOther ?? '',
        'lgbtq' => $user->userDemoLgbtq?->value ?? '',
        'freeReducedEligible' => $user->userFreeReducedEligible ?? '',
        'hearAbout' => $user->userHearAbout?->value ?? '',
        'interests' => ArrayHelper::getColumn(
          $user->userInterests,
          'value'
        ),
        'referred' => $user->userReferred?->value ?? '',
        'adultEmailAddress' => $user->userAdultEmailAddress ?? '',
        'adultName' => $user->userAdultName ?? '',
        'adultRelationshipToTeen' => $user->userAdultRelationshipToTeen?->value ?? '',
        'applicantType' => $user->userApplicantType?->value ?? '',
      ];
    }

    return $results;
 
  }
}