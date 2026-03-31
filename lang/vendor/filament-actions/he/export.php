<?php

return [

    'label' => 'ייצוא :label',

    'modal' => [

        'heading' => 'ייצוא :label',

        'form' => [

            'columns' => [

                'label' => 'עמודות',

                'form' => [

                    'is_enabled' => [
                        'label' => ':column פעיל',
                    ],

                    'label' => [
                        'label' => 'תווית :column',
                    ],

                ],

            ],

        ],

        'actions' => [

            'export' => [
                'label' => 'ייצוא',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'הייצוא הושלם',

            'actions' => [

                'download_csv' => [
                    'label' => 'הורד ‎.csv',
                ],

                'download_xlsx' => [
                    'label' => 'הורד ‎.xlsx',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'הייצוא גדול מדי',
            'body' => 'אין לייצא יותר משורה אחת בכל פעם.|אין לייצא יותר מ-:count שורות בכל פעם.',
        ],

        'started' => [
            'title' => 'הייצוא התחיל',
            'body' => 'הייצוא שלך התחיל ושורה אחת תעובד ברקע. תקבל התראה עם קישור להורדה כשהוא יושלם.|הייצוא שלך התחיל ו-:count שורות יעובדו ברקע. תקבל התראה עם קישור להורדה כשהוא יושלם.',
        ],

    ],

    'file_name' => 'export-:export_id-:model',

];
