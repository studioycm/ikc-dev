<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Language display name
    |--------------------------------------------------------------------------
    |
    | Option to whether display the language in English or Native.
    |
    */

    'native' => true,

    /*
    |--------------------------------------------------------------------------
    | Flag
    |--------------------------------------------------------------------------
    |
    | Option to display flag for the Language.
    | By default the first and second letter of the display name (if single word, otherwise first letter of first two words) will be used instead of flag.
    | If set to true, the following package needs to be installed via composer.
    | "composer require stijnvanouplines/blade-country-flags"
    */

    'flag' => false,

    /*
    |--------------------------------------------------------------------------
    | All Locales (Languages)
    |--------------------------------------------------------------------------
    |
    | Uncomment the languages that your site supports - or add new ones.
    | These are sorted by the native name, which is the order you might show them in a language selector.
    |
    */

    'locales' => [
        'en' => ['name' => 'English',                'script' => 'Latn', 'native' => 'English', 'flag_code' => 'us'],
        'he' => ['name' => 'Hebrew',                 'script' => 'Hebr', 'native' => 'עברית', 'flag_code' => '' ],
    ],
];
