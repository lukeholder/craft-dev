<?php

/**
 * UserBasicExporter
 * 
 * Provides a custom exporter for user elements, only exporting the fields that are needed for the common User Pass Report.
 */

namespace modules\sitemodule;

use craft\base\ElementExporter;
use craft\elements\User;
use craft\elements\db\ElementQueryInterface;
use craft\helpers\ArrayHelper;

class UserBasicExporter extends ElementExporter {

    public static function displayName(): string {
    return 'User data (basic)';
  }

  public function export(ElementQueryInterface $query): array {

    $results = [];

    foreach ($query->each() as $user) {
      $results[] = [
        'id' => $user->id ?? '',
        'dateCreated' => $user->dateCreated?->format("M-d-Y") ?? '',
        'site' => $user->userSite ?? '',
        'firstName' => $user->firstName ?? '',
        'lastName' => $user->lastName ?? '',
        'birthdate' => $user->userBirthdate?->format("M-d-Y") ?? '',
        'city' => $user->userCity ?? '',
        'state' => $user->userState ?? '',
        'zipcode' => $user->userZipcode ?? '',
        'ethnicity' => ArrayHelper::getColumn(
          $user->userDemoEthnicity,
          'value'
        ),
        'gender' => $user->userDemoGender?->value ?? '',
        'genderOther' => $user->userDemoGenderOther ?? '',
      ];
    }

    return $results;
 
  }
}