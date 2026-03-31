<?php

return [

    'label' => 'בונה שאילתות',

    'form' => [

        'operator' => [
            'label' => 'אופרטור',
        ],

        'or_groups' => [

            'label' => 'קבוצות',

            'block' => [
                'label' => 'חלופה (OR)',
                'or' => 'OR',
            ],

        ],

        'rules' => [

            'label' => 'כללים',

            'item' => [
                'and' => 'AND',
            ],

        ],

    ],

    'no_rules' => '(אין כללים)',

    'item_separators' => [
        'and' => 'AND',
        'or' => 'OR',
    ],

    'operators' => [

        'is_filled' => [

            'label' => [
                'direct' => 'מלא',
                'inverse' => 'ריק',
            ],

            'summary' => [
                'direct' => 'ל-:attribute יש ערך',
                'inverse' => ':attribute ריק',
            ],

        ],

        'boolean' => [

            'is_true' => [

                'label' => [
                    'direct' => 'הוא true',
                    'inverse' => 'הוא false',
                ],

                'summary' => [
                    'direct' => ':attribute הוא true',
                    'inverse' => ':attribute הוא false',
                ],

            ],

        ],

        'date' => [

            'is_after' => [

                'label' => [
                    'direct' => 'אחרי',
                    'inverse' => 'לא אחרי',
                ],

                'summary' => [
                    'direct' => ':attribute אחרי :date',
                    'inverse' => ':attribute לא אחרי :date',
                ],

            ],

            'is_before' => [

                'label' => [
                    'direct' => 'לפני',
                    'inverse' => 'לא לפני',
                ],

                'summary' => [
                    'direct' => ':attribute לפני :date',
                    'inverse' => ':attribute לא לפני :date',
                ],

            ],

            'is_date' => [

                'label' => [
                    'direct' => 'הוא תאריך',
                    'inverse' => 'אינו תאריך',
                ],

                'summary' => [
                    'direct' => ':attribute הוא :date',
                    'inverse' => ':attribute אינו :date',
                ],

            ],

            'is_month' => [

                'label' => [
                    'direct' => 'הוא חודש',
                    'inverse' => 'אינו חודש',
                ],

                'summary' => [
                    'direct' => ':attribute הוא :month',
                    'inverse' => ':attribute אינו :month',
                ],

            ],

            'is_year' => [

                'label' => [
                    'direct' => 'הוא שנה',
                    'inverse' => 'אינו שנה',
                ],

                'summary' => [
                    'direct' => ':attribute הוא :year',
                    'inverse' => ':attribute אינו :year',
                ],

            ],

            'form' => [

                'date' => [
                    'label' => 'תאריך',
                ],

                'month' => [
                    'label' => 'חודש',
                ],

                'year' => [
                    'label' => 'שנה',
                ],

            ],

        ],

        'number' => [

            'equals' => [

                'label' => [
                    'direct' => 'שווה ל',
                    'inverse' => 'לא שווה ל',
                ],

                'summary' => [
                    'direct' => ':attribute שווה ל-:number',
                    'inverse' => ':attribute לא שווה ל-:number',
                ],

            ],

            'is_max' => [

                'label' => [
                    'direct' => 'מקסימום',
                    'inverse' => 'גדול מ-',
                ],

                'summary' => [
                    'direct' => ':attribute לכל היותר :number',
                    'inverse' => ':attribute גדול מ-:number',
                ],

            ],

            'is_min' => [

                'label' => [
                    'direct' => 'מינימום',
                    'inverse' => 'קטן מ-',
                ],

                'summary' => [
                    'direct' => ':attribute לפחות :number',
                    'inverse' => ':attribute קטן מ-:number',
                ],

            ],

            'aggregates' => [

                'average' => [
                    'label' => 'ממוצע',
                    'summary' => 'ממוצע של :attribute',
                ],

                'max' => [
                    'label' => 'מקסימום',
                    'summary' => 'מקסימום של :attribute',
                ],

                'min' => [
                    'label' => 'מינימום',
                    'summary' => 'מינימום של :attribute',
                ],

                'sum' => [
                    'label' => 'סכום',
                    'summary' => 'סכום של :attribute',
                ],

            ],

            'form' => [

                'aggregate' => [
                    'label' => 'אגרגציה',
                ],

                'number' => [
                    'label' => 'מספר',
                ],

            ],

        ],

        'relationship' => [

            'equals' => [

                'label' => [
                    'direct' => 'מכיל',
                    'inverse' => 'לא מכיל',
                ],

                'summary' => [
                    'direct' => 'מכיל :count :relationship',
                    'inverse' => 'לא מכיל :count :relationship',
                ],

            ],

            'has_max' => [

                'label' => [
                    'direct' => 'מכיל לכל היותר',
                    'inverse' => 'מכיל יותר מ-',
                ],

                'summary' => [
                    'direct' => 'מכיל לכל היותר :count :relationship',
                    'inverse' => 'מכיל יותר מ-:count :relationship',
                ],

            ],

            'has_min' => [

                'label' => [
                    'direct' => 'מכיל לפחות',
                    'inverse' => 'מכיל פחות מ-',
                ],

                'summary' => [
                    'direct' => 'מכיל לפחות :count :relationship',
                    'inverse' => 'מכיל פחות מ-:count :relationship',
                ],

            ],

            'is_empty' => [

                'label' => [
                    'direct' => 'ריק',
                    'inverse' => 'אינו ריק',
                ],

                'summary' => [
                    'direct' => ':relationship ריק',
                    'inverse' => ':relationship אינו ריק',
                ],

            ],

            'is_related_to' => [

                'label' => [

                    'single' => [
                        'direct' => 'הוא',
                        'inverse' => 'אינו',
                    ],

                    'multiple' => [
                        'direct' => 'מכיל',
                        'inverse' => 'לא מכיל',
                    ],

                ],

                'summary' => [

                    'single' => [
                        'direct' => ':relationship הוא :values',
                        'inverse' => ':relationship אינו :values',
                    ],

                    'multiple' => [
                        'direct' => ':relationship מכיל :values',
                        'inverse' => ':relationship לא מכיל :values',
                    ],

                    'values_glue' => [
                        0 => ', ',
                        'final' => ' או ',
                    ],

                ],

                'form' => [

                    'value' => [
                        'label' => 'ערך',
                    ],

                    'values' => [
                        'label' => 'ערכים',
                    ],

                ],

            ],

            'form' => [

                'count' => [
                    'label' => 'כמות',
                ],

            ],

        ],

        'select' => [

            'is' => [

                'label' => [
                    'direct' => 'הוא',
                    'inverse' => 'אינו',
                ],

                'summary' => [
                    'direct' => ':attribute הוא :values',
                    'inverse' => ':attribute אינו :values',
                    'values_glue' => [
                        ', ',
                        'final' => ' או ',
                    ],
                ],

                'form' => [

                    'value' => [
                        'label' => 'ערך',
                    ],

                    'values' => [
                        'label' => 'ערכים',
                    ],

                ],

            ],

        ],

        'text' => [

            'contains' => [

                'label' => [
                    'direct' => 'מכיל',
                    'inverse' => 'לא מכיל',
                ],

                'summary' => [
                    'direct' => ':attribute מכיל :text',
                    'inverse' => ':attribute לא מכיל :text',
                ],

            ],

            'ends_with' => [

                'label' => [
                    'direct' => 'מסתיים ב-',
                    'inverse' => 'לא מסתיים ב-',
                ],

                'summary' => [
                    'direct' => ':attribute מסתיים ב-:text',
                    'inverse' => ':attribute לא מסתיים ב-:text',
                ],

            ],

            'equals' => [

                'label' => [
                    'direct' => 'שווה ל',
                    'inverse' => 'לא שווה ל',
                ],

                'summary' => [
                    'direct' => ':attribute שווה ל-:text',
                    'inverse' => ':attribute לא שווה ל-:text',
                ],

            ],

            'starts_with' => [

                'label' => [
                    'direct' => 'מתחיל ב-',
                    'inverse' => 'לא מתחיל ב-',
                ],

                'summary' => [
                    'direct' => ':attribute מתחיל ב-:text',
                    'inverse' => ':attribute לא מתחיל ב-:text',
                ],

            ],

            'form' => [

                'text' => [
                    'label' => 'טקסט',
                ],

            ],

        ],

    ],

    'actions' => [

        'add_rule' => [
            'label' => 'הוסף כלל',
        ],

        'add_rule_group' => [
            'label' => 'הוסף קבוצת כללים',
        ],

    ],

];
