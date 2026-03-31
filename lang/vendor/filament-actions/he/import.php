<?php

return [

    'label' => 'ייבוא :label',

    'modal' => [

        'heading' => 'ייבוא :label',

        'form' => [

            'file' => [

                'label' => 'קובץ',

                'placeholder' => 'העלה קובץ CSV',

                'rules' => [
                    'duplicate_columns' => '{0} הקובץ לא יכול להכיל יותר מכותרת עמודה ריקה אחת.|{1,*} הקובץ לא יכול להכיל כותרות עמודות כפולות: :columns.',
                ],

            ],

            'columns' => [
                'label' => 'עמודות',
                'placeholder' => 'בחר עמודה',
            ],

        ],

        'actions' => [

            'download_example' => [
                'label' => 'הורד קובץ CSV לדוגמה',
            ],

            'import' => [
                'label' => 'ייבוא',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'הייבוא הושלם',

            'actions' => [

                'download_failed_rows_csv' => [
                    'label' => 'הורד מידע על השורה שנכשלה|הורד מידע על השורות שנכשלו',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'קובץ ה-CSV שהועלה גדול מדי',
            'body' => 'אין לייבא יותר משורה אחת בכל פעם.|אין לייבא יותר מ-:count שורות בכל פעם.',
        ],

        'started' => [
            'title' => 'הייבוא התחיל',
            'body' => 'הייבוא שלך התחיל ושורה אחת תעובד ברקע.|הייבוא שלך התחיל ו-:count שורות יעובדו ברקע.',
        ],

    ],

    'example_csv' => [
        'file_name' => ':importer-example',
    ],

    'failure_csv' => [
        'file_name' => 'import-:import_id-:csv_name-failed-rows',
        'error_header' => 'שגיאה',
        'system_error' => 'שגיאת מערכת, נא לפנות לתמיכה.',
        'column_mapping_required_for_new_record' => 'העמודה :attribute לא הותאמה לעמודה בקובץ, אך היא נדרשת ליצירת רשומות חדשות.',
    ],

];
