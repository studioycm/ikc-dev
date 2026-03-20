<?php

return [
    'defaults' => [
        'depth' => 4,
        'density' => 'comfortable',
        'font_scale' => 'normal',
        'card_height' => 'normal',
        'root_titles_mode' => 'comfortable',
        'show_placeholders' => true,
        'visible_fields' => [
            'sagir_id' => true,
            'import_number' => false,
            'breeding_house' => true,
            'breed_name' => false,
            'color_name' => true,
            'birth_date' => true,
            'age' => false,
            'titles' => true,
        ],
    ],
    'presets' => [
        'resource_view' => [],
        'resource_edit' => [],
        'user_widget_modal' => [
            'depth' => 4,
            'visible_fields' => [
                'titles' => true,
                'age' => false,
            ],
        ],
    ],
];
