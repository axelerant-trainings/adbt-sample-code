<?php

/**
 * Implements hook_views_data().
 *
 * Provides the Views data for the custom fitness_workouts table.
 *
 * @return array
 *   An array defining the Views data for the fitness_workouts table.
 */
function fitness_challenge_views_data() : array {
  $data = [];

  // Define the base table.
  $data['fitness_workouts'] = [
    'table' => [
      'group' => t('Fitness Workouts'),
      'base' => [
        'field' => 'id',
        'title' => t('Fitness Workouts'),
        'help' => t('Stores individual workout records, including type, duration, date, and image.'),
        'weight' => 0,
      ],
    ],
  ];

  // Field for workout ID (primary key).
  $data['fitness_workouts']['id'] = [
    'title' => t('Workout ID'),
    'help' => t('The unique ID of the workout record.'),
    'field' => [
      'id' => 'numeric',
    ],
  ];

  // Field for the user who logged the workout.
  $data['fitness_workouts']['uid'] = [
    'title' => t('User ID'),
    'help' => t('The user who logged the workout.'),
    'field' => [
      'id' => 'numeric',
    ],
    'relationship' => [
      'title' => t('User'),
      'help' => t('Relates this workout to the user who logged it.'),
      'base' => 'users_field_data',
      'base field' => 'uid',
      'relationship field' => 'uid',
      'id' => 'standard',
    ],
  ];

  // Field for workout type.
  $data['fitness_workouts']['workout_type'] = [
    'title' => t('Workout Type'),
    'help' => t('The type of workout (e.g., cardio, strength).'),
    'field' => [
      'id' => 'standard',
    ],
    'filter' => [
      'id' => 'string',
    ],
  ];

  // Field for workout duration.
  $data['fitness_workouts']['duration'] = [
    'title' => t('Workout Duration'),
    'help' => t('The duration of the workout in minutes.'),
    'field' => [
      'id' => 'numeric',
    ],
    'filter' => [
      'id' => 'numeric',
    ],
  ];

  // Field for workout date.
  $data['fitness_workouts']['date'] = [
    'title' => t('Workout Date'),
    'help' => t('The date when the workout was logged.'),
    'field' => [
      'id' => 'date',
    ],
    'filter' => [
      'id' => 'date',
    ],
  
  ];

  $data['fitness_workouts']['image_fid'] = [
    'title' => t('Image File ID'),
    'help' => t('The file ID of the uploaded image.'),
    'field' => [
      'id' => 'numeric',
    ],
    'relationship' => [
      'base' => 'file_managed',  // The base table for file entities.
      'base field' => 'fid',  // Join the 'fid' field with the 'file_id' in the custom table.
      'id' => 'standard',
      'label' => t('Managed File FID'),
    ],
  ];

  return $data;
}
