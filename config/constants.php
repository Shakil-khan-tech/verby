<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Colors
    | Here are general color ordering
    |--------------------------------------------------------------------------
    */

    'colors' => [ 'primary', 'success', 'info', 'warning', 'danger', 'medium-danger', 'light-danger', 'green', 'medium-green', 'light-green'],

    /*
    |--------------------------------------------------------------------------
    | Actions
    | Here are device actions by employees
    |--------------------------------------------------------------------------
    */

    'actions' => [ 'Checkin', 'Checkout', 'Pause In', 'Pause Out'],

    /*
    |--------------------------------------------------------------------------
    | Performs
    | Here are device performs by employees
    |--------------------------------------------------------------------------
    */

    'performs' => [ 'Stewarding', 'Unterhalt', 'Gouvernante', 'Raumpflegerin', 'Büro' ],

    /*
    |--------------------------------------------------------------------------
    | Identities
    | How emplozees were authenticated in device
    |--------------------------------------------------------------------------
    */

    'identities' => [ 'Card', 'Pin', 'Camera', 'PC'],

    /*
    |--------------------------------------------------------------------------
    | Room clean type
    | Used in calendar, should the room be cleaned as depa or restant
    |--------------------------------------------------------------------------
    */

    'clean_types' => [ 'Depa', 'Restant'],

    /*
    |--------------------------------------------------------------------------
    | Room assigned in calendar
    | Used in calendar, how the room should be assigned in special occasions
    |--------------------------------------------------------------------------
    */

    'calendar_room_extra' => [ 'Normal', 'WW', 'VIP', 'Showroom', 'Unterhalt 1', 'Unterhalt 2', 'Unterhalt 3', 'Stewarding 1', 'Stewarding 2', 'Stewarding 3' ],

        /*
    |--------------------------------------------------------------------------
    | Room assigned in calendar
    | Used in calendar, how the room should be assigned in special occasions
    |--------------------------------------------------------------------------
    */

    'room_status' => [ 'Uncleaned', 'Cleaned', 'Red Card', 'Volunteer' ],

    /*
    |--------------------------------------------------------------------------
    | Room Categories
    | How emplozees were authenticated in device
    |--------------------------------------------------------------------------
    */

    'room_categories' => [ 'Standard', 'Apartment', 'Suite', 'Junior Suite', 'Executive Suite', 'President Suite', 'Grand Suite'],

    /*
    |--------------------------------------------------------------------------
    | Kantons
    |--------------------------------------------------------------------------
    */

    'kantons' => [
        'AG' => 'AG',
        'AR' => 'AR',
        'BE' => 'BE',
        'BL' => 'BL',
        'BS' => 'BS',
        'FR' => 'FR',
        'GE' => 'GE',
        'GL' => 'GL',
        'GR' => 'GR',
        'JU' => 'JU',
        'LU' => 'LU',
        'NE' => 'NE',
        'NW' => 'NW',
        'OW' => 'OW',
        'SG' => 'SG',
        'SH' => 'SH',
        'SO' => 'SO',
        'SZ' => 'SZ',
        'TG' => 'TG',
        'TI' => 'TI',
        'UR' => 'UR',
        'VD' => 'VD',
        'VS' => 'VS',
        'ZG' => 'ZG',
        'ZH' => 'ZH'
    ],

    /*
    |--------------------------------------------------------------------------
    | Roli == Funtion
    |--------------------------------------------------------------------------
    */

    'functions' => [ 'Gouvernante', 'Raumpflegerinnen', 'Unterhalt', 'Stewarding', 'Zimmer Controle', 'Zimmer Reinigung','Annuliert', 'Assistant Housekeeper', 'Allrounder', 'Late Shift', 'Night Shift', 'Office'],

    /*
    |--------------------------------------------------------------------------
    | Role == Rolle
    |--------------------------------------------------------------------------
    */

    //Përdorues, Menaxher
    // 'roles' => [ 'Benutzer', 'Manager' ],

    /*
    |--------------------------------------------------------------------------
    | PartTime
    |--------------------------------------------------------------------------
    */

    'part_time' => [ 'Monat', 'Hourly' ],


    /*
    |--------------------------------------------------------------------------
    | Functions colors
    |--------------------------------------------------------------------------
    */

    'funktion_colors' => [ 'primary', 'success', 'warning', 'info' ],

    /*
    |--------------------------------------------------------------------------
    | Priorities
    |--------------------------------------------------------------------------
    */

    'priorities' => [ 'Low', 'Medium', 'High', 'Urgent' ],
    'priority_colors' => [ 'success', 'primary', 'warning', 'danger'],

    /*
    |--------------------------------------------------------------------------
    | Hotels
    |--------------------------------------------------------------------------
    */

    'plans' => [
        'F' => [
          'color' => 'bg-green-200',
          'class' => 'light-success',
          'symbol_class' => '',
          'text'  => 'Holiday', //Ferien
        ],
        'W' => [
          'color' => 'bg-purple-300',
          'class' => 'light-info',
          'symbol_class' => '',
          'text'  => 'Free request', //Wunsch Frei
        ],
        'S' => [
          'color' => 'bg-blue-200',
          'class' => 'light-primary',
          'symbol_class' => '',
          'text'  => 'Pregnant', //Schwanger
        ],
        'A' => [
          'color' => 'bg-red-300',
          'class' => 'danger',
          'symbol_class' => '',
          'text'  => 'Departure', //Austritt
        ],
        'K' => [
          'color' => 'bg-yellow-100',
          'class' => 'light-warning',
          'symbol_class' => '',
          'text'  => 'Sick', //Krank
        ],
        'KK' => [
          'color' => 'bg-yellow-400',
          'class' => 'dark-warning',
          'symbol_class' => 'bg-warning-o-100',
          'text'  => 'Child Sick', //Kind Krank
        ],
        'O' => [
          'color' => 'bg-blue-400',
          'class' => 'dark-primary',
          'symbol_class' => 'bg-primary-o-100',
          'text'  => 'Object change', //Objektwechsel
        ],
        'U' => [
          'color' => 'bg-pink-400',
          'class' => 'light-danger',
          'symbol_class' => '',
          'text'  => 'Accident', //Unfall
        ],
        'V' => [
          'color' => 'bg-dark-o-40',
          'class' => 'light-dark',
          'symbol_class' => '',
          'text'  => 'Volunteer', //Volunteer
        ],
        'FR' => [
          'color' => 'bg-dark-o-80',
          'class' => 'dark',
          'symbol_class' => '',
          'text'  => 'Free', //Frei
        ],
        'SC' => [
          'color' => 'bg-danger-o-35',
          'class' => 'light-danger',
          'symbol_class' => '',
          'text'  => 'School', //Schule
        ],
        'MSE' => [
          'color' => 'bg-danger-o-40',
          'class' => 'light-dark',
          'symbol_class' => '',
          'text'  => 'MSE',
        ],
        'VSE' => [
          'color' => 'bg-dark-o-10',
          'class' => 'light-dark',
          'symbol_class' => '',
          'text'  => 'VSE',
        ],
        'UN' => [
          'color' => 'bg-dark-o-20',
          'class' => 'light-dark',
          'symbol_class' => '',
          'text'  => 'Unexcused', //Unentschuldigt
        ],
    ],

    // 'plan_color' => function($symbol){
    //   if (isset(  Config::get('constants.plans')[$symbol]['color'] )) {
    //     return Config::get('constants.plans')[$symbol]['color'];
    //   } else {
    //     return '';
    //   }
    // },

    'plan_dayofweek' => [ 'bg-red-50', '', '', '', '', '', 'bg-yellow-50', ],
];
