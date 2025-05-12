<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted'             => 'השדה :attribute חייב להיות מאושר.',
    'accepted_if'          => 'השדה :attribute חייב להיות מאושר כאשר :other הוא :value.',
    'active_url'           => 'השדה :attribute חייב להיות כתובת URL חוקית.',
    'after'                => 'השדה :attribute חייב להיות תאריך לאחר :date.',
    'after_or_equal'       => 'השדה :attribute חייב להיות תאריך לאחר או שווה ל־:date.',
    'alpha'                => 'השדה :attribute חייב להכיל רק אותיות.',
    'alpha_dash'           => 'השדה :attribute חייב להכיל רק אותיות, מספרים, מקפים וקווים תחתונים.',
    'alpha_num'            => 'השדה :attribute חייב להכיל רק אותיות ומספרים.',
    'array'                => 'השדה :attribute חייב להיות מערך.',
    'ascii'                => 'השדה :attribute חייב להכיל רק תווים אלפאנומריים וסימנים של בית אחד.',
    'before'               => 'השדה :attribute חייב להיות תאריך לפני :date.',
    'before_or_equal'      => 'השדה :attribute חייב להיות תאריך לפני או שווה ל־:date.',
    'between'              => [
        'array'   => 'השדה :attribute חייב להכיל בין :min ל־:max פריטים.',
        'file'    => 'השדה :attribute חייב להיות בין :min ל־:max קילובייטים.',
        'numeric' => 'השדה :attribute חייב להיות בין :min ל־:max.',
        'string'  => 'השדה :attribute חייב להיות בין :min ל־:max תווים.',
    ],
    'boolean'              => 'השדה :attribute חייב להיות true או false.',
    'can'                  => 'השדה :attribute מכיל ערך ללא הרשאה.',
    'confirmed'            => 'אימות השדה :attribute אינו תואם.',
    'contains'             => 'השדה :attribute חסר ערך נדרש.',
    'current_password'     => 'הסיסמה שגויה.',
    'date'                 => 'השדה :attribute חייב להיות תאריך חוקי.',
    'date_equals'          => 'השדה :attribute חייב להיות תאריך השווה ל־:date.',
    'date_format'          => 'השדה :attribute חייב להתאים לפורמט :format.',
    'decimal'              => 'השדה :attribute חייב להכיל :decimal ספרות עשרוניות.',
    'declined'             => 'השדה :attribute חייב להיות נדחה.',
    'declined_if'          => 'השדה :attribute חייב להיות נדחה כאשר :other הוא :value.',
    'different'            => 'השדה :attribute ו־:other חייבים להיות שונים.',
    'digits'               => 'השדה :attribute חייב להיות באורך :digits ספרות.',
    'digits_between'       => 'השדה :attribute חייב להכיל בין :min ל־:max ספרות.',
    'dimensions'           => 'לשדה :attribute ממדי תמונה לא חוקיים.',
    'distinct'             => 'לשדה :attribute יש ערך כפול.',
    'doesnt_end_with'      => 'השדה :attribute חייב לא להסתיים באחד מהערכים: :values.',
    'doesnt_start_with'    => 'השדה :attribute חייב לא להתחיל באחד מהערכים: :values.',
    'email'                => 'השדה :attribute חייב להיות כתובת דוא"ל חוקית.',
    'ends_with'            => 'השדה :attribute חייב להסתיים באחד מהערכים: :values.',
    'enum'                 => 'הערך שנבחר עבור :attribute אינו חוקי.',
    'exists'               => 'הערך שנבחר עבור :attribute אינו חוקי.',
    'extensions'           => 'השדה :attribute חייב להיות עם אחת מהסיומות: :values.',
    'file'                 => 'השדה :attribute חייב להיות קובץ.',
    'filled'               => 'השדה :attribute חייב להכיל ערך.',
    'gt'                   => [
        'array'   => 'השדה :attribute חייב להכיל יותר מ־:value פריטים.',
        'file'    => 'השדה :attribute חייב להיות גדול מ־:value קילובייטים.',
        'numeric' => 'השדה :attribute חייב להיות גדול מ־:value.',
        'string'  => 'השדה :attribute חייב להיות יותר מ־:value תווים.',
    ],
    'gte'                  => [
        'array'   => 'השדה :attribute חייב להכיל לפחות :value פריטים.',
        'file'    => 'השדה :attribute חייב להיות גדול או שווה ל־:value קילובייטים.',
        'numeric' => 'השדה :attribute חייב להיות גדול או שווה ל־:value.',
        'string'  => 'השדה :attribute חייב להיות גדול או שווה ל־:value תווים.',
    ],
    'hex_color'            => 'השדה :attribute חייב להיות צבע הקסה חוקי.',
    'image'                => 'השדה :attribute חייב להיות תמונה.',
    'in'                   => 'הערך שנבחר עבור :attribute אינו חוקי.',
    'in_array'             => 'השדה :attribute חייב להיות קיים ב־:other.',
    'integer'              => 'השדה :attribute חייב להיות מספר שלם.',
    'ip'                   => 'השדה :attribute חייב להיות כתובת IP חוקית.',
    'ipv4'                 => 'השדה :attribute חייב להיות כתובת IPv4 חוקית.',
    'ipv6'                 => 'השדה :attribute חייב להיות כתובת IPv6 חוקית.',
    'json'                 => 'השדה :attribute חייב להיות מחרוזת JSON חוקית.',
    'list'                 => 'השדה :attribute חייב להיות רשימה.',
    'lowercase'            => 'השדה :attribute חייב להיות באותיות קטנות.',
    'lt'                   => [
        'array'   => 'השדה :attribute חייב להכיל פחות מ־:value פריטים.',
        'file'    => 'השדה :attribute חייב להיות פחות מ־:value קילובייטים.',
        'numeric' => 'השדה :attribute חייב להיות פחות מ־:value.',
        'string'  => 'השדה :attribute חייב להיות פחות מ־:value תווים.',
    ],
    'lte'                  => [
        'array'   => 'השדה :attribute חייב לא להכיל יותר מ־:value פריטים.',
        'file'    => 'השדה :attribute חייב להיות קטן או שווה ל־:value קילובייטים.',
        'numeric' => 'השדה :attribute חייב להיות קטן או שווה ל־:value.',
        'string'  => 'השדה :attribute חייב להיות קטן או שווה ל־:value תווים.',
    ],
    'mac_address'          => 'השדה :attribute חייב להיות כתובת MAC חוקית.',
    'max'                  => [
        'array'   => 'השדה :attribute חייב לא להכיל יותר מ־:max פריטים.',
        'file'    => 'השדה :attribute חייב לא להיות גדול מ־:max קילובייטים.',
        'numeric' => 'השדה :attribute חייב לא להיות גדול מ־:max.',
        'string'  => 'השדה :attribute חייב לא להיות ארוך יותר מ־:max תווים.',
    ],
    'max_digits'           => 'השדה :attribute חייב לא להכיל יותר מ־:max ספרות.',
    'mimes'                => 'השדה :attribute חייב להיות קובץ מהסוגים: :values.',
    'mimetypes'            => 'השדה :attribute חייב להיות קובץ מהסוגים: :values.',
    'min'                  => [
        'array'   => 'השדה :attribute חייב להכיל לפחות :min פריטים.',
        'file'    => 'השדה :attribute חייב להיות לפחות :min קילובייטים.',
        'numeric' => 'השדה :attribute חייב להיות לפחות :min.',
        'string'  => 'השדה :attribute חייב להיות לפחות :min תווים.',
    ],
    'min_digits'           => 'השדה :attribute חייב להכיל לפחות :min ספרות.',
    'missing'              => 'השדה :attribute חייב להיות חסר.',
    'missing_if'           => 'השדה :attribute חייב להיות חסר כאשר :other הוא :value.',
    'missing_unless'       => 'השדה :attribute חייב להיות חסר אלא אם :other הוא :value.',
    'missing_with'         => 'השדה :attribute חייב להיות חסר כאשר :values נוכחים.',
    'missing_with_all'     => 'השדה :attribute חייב להיות חסר כאשר כל :values נוכחים.',
    'multiple_of'          => 'השדה :attribute חייב להיות כפולה של :value.',
    'not_in'               => 'הערך שנבחר עבור :attribute אינו חוקי.',
    'not_regex'            => 'פורמט השדה :attribute אינו חוקי.',
    'numeric'              => 'השדה :attribute חייב להיות מספר.',
    'password'             => [
        'letters'       => 'השדה :attribute חייב להכיל לפחות אות אחת.',
        'mixed'         => 'השדה :attribute חייב להכיל לפחות אות גדולה ואות קטנה.',
        'numbers'       => 'השדה :attribute חייב להכיל לפחות מספר אחד.',
        'symbols'       => 'השדה :attribute חייב להכיל לפחות סימן אחד.',
        'uncompromised' => 'השדה :attribute שהוזן הופיע בדליפת מידע. אנא בחר :attribute אחר.',
    ],
    'present'              => 'השדה :attribute חייב להיות נוכח.',
    'present_if'           => 'השדה :attribute חייב להיות נוכח כאשר :other הוא :value.',
    'present_unless'       => 'השדה :attribute חייב להיות נוכח אלא אם :other הוא :value.',
    'present_with'         => 'השדה :attribute חייב להיות נוכח כאשר :values נוכחים.',
    'present_with_all'     => 'השדה :attribute חייב להיות נוכח כאשר כל :values נוכחים.',
    'prohibited'           => 'השדה :attribute אסור.',
    'prohibited_if'        => 'השדה :attribute אסור כאשר :other הוא :value.',
    'prohibited_if_accepted' => 'השדה :attribute אסור כאשר :other מתקבל.',
    'prohibited_if_declined' => 'השדה :attribute אסור כאשר :other נדחה.',
    'prohibited_unless'    => 'השדה :attribute אסור אלא אם :other הוא באחד מהערכים: :values.',
    'prohibits'            => 'השדה :attribute אוסר על נוכחות :other.',
    'regex'                => 'פורמט השדה :attribute אינו חוקי.',
    'required'             => 'השדה :attribute הוא שדה חובה.',
    'required_array_keys'  => 'השדה :attribute חייב להכיל ערכים עבור: :values.',
    'required_if'          => 'השדה :attribute נדרש כאשר :other הוא :value.',
    'required_if_accepted' => 'השדה :attribute נדרש כאשר :other מתקבל.',
    'required_if_declined' => 'השדה :attribute נדרש כאשר :other נדחה.',
    'required_unless'      => 'השדה :attribute נדרש אלא אם :other הוא באחד מהערכים: :values.',
    'required_with'        => 'השדה :attribute נדרש כאשר :values נוכחים.',
    'required_with_all'    => 'השדה :attribute נדרש כאשר כל :values נוכחים.',
    'required_without'     => 'השדה :attribute נדרש כאשר :values אינם נוכחים.',
    'required_without_all' => 'השדה :attribute נדרש כאשר אף אחד מהערכים :values אינו נוכח.',
    'same'                 => 'השדה :attribute חייב להתאים לשדה :other.',
    'size'                 => [
        'array'   => 'השדה :attribute חייב להכיל :size פריטים.',
        'file'    => 'השדה :attribute חייב להיות בגודל :size קילובייטים.',
        'numeric' => 'השדה :attribute חייב להיות :size.',
        'string'  => 'השדה :attribute חייב להיות :size תווים.',
    ],
    'starts_with'          => 'השדה :attribute חייב להתחיל באחד מהערכים: :values.',
    'string'               => 'השדה :attribute חייב להיות מחרוזת.',
    'timezone'             => 'השדה :attribute חייב להיות איזור זמן חוקי.',
    'unique'               => 'השדה :attribute כבר בשימוש.',
    'uploaded'             => 'העלאת השדה :attribute נכשלה.',
    'uppercase'            => 'השדה :attribute חייב להיות באותיות גדולות.',
    'url'                  => 'השדה :attribute חייב להיות כתובת URL חוקית.',
    'ulid'                 => 'השדה :attribute חייב להיות ULID חוקי.',
    'uuid'                 => 'השדה :attribute חייב להיות UUID חוקי.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [],

];
