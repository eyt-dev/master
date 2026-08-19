<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines (Arabic)
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class translated to Arabic.
    |
    */

    'accepted' => 'يجب قبول حقل :attribute.',
    'active_url' => 'حقل :attribute ليس عنوان URL صالحاً.',
    'after' => 'يجب أن يكون حقل :attribute تاريخاً بعد :date.',
    'after_or_equal' => 'يجب أن يكون حقل :attribute تاريخاً بعد أو مساوياً :date.',
    'alpha' => 'قد يحتوي حقل :attribute على أحرف فقط.',
    'alpha_dash' => 'قد يحتوي حقل :attribute على أحرف وأرقام وشرطات وشرطات سفلية فقط.',
    'alpha_num' => 'قد يحتوي حقل :attribute على أحرف وأرقام فقط.',
    'array' => 'يجب أن يكون حقل :attribute مصفوفة.',
    'before' => 'يجب أن يكون حقل :attribute تاريخاً قبل :date.',
    'before_or_equal' => 'يجب أن يكون حقل :attribute تاريخاً قبل أو مساوياً :date.',
    'between' => [
        'numeric' => 'يجب أن يكون حقل :attribute بين :min و :max.',
        'file' => 'يجب أن يكون حقل :attribute بين :min و :max كيلوبايت.',
        'string' => 'يجب أن يكون حقل :attribute بين :min و :max أحرف.',
        'array' => 'يجب أن يحتوي حقل :attribute على بين :min و :max عناصر.',
    ],
    'boolean' => 'يجب أن يكون حقل :attribute صحيح أو خاطئ.',
    'confirmed' => 'تأكيد حقل :attribute لا يطابق.',
    'date' => 'حقل :attribute ليس تاريخاً صالحاً.',
    'date_equals' => 'يجب أن يكون حقل :attribute تاريخاً مساوياً :date.',
    'date_format' => 'حقل :attribute لا يطابق الصيغة :format.',
    'different' => 'يجب أن يكون حقل :attribute و :other مختلفين.',
    'digits' => 'يجب أن يكون حقل :attribute :digits أرقام.',
    'digits_between' => 'يجب أن يكون حقل :attribute بين :min و :max أرقام.',
    'dimensions' => 'حقل :attribute يحتوي على أبعاد صورة غير صالحة.',
    'distinct' => 'حقل :attribute يحتوي على قيمة مكررة.',
    'email' => 'يجب أن يكون حقل :attribute عنوان بريد إلكتروني صالحاً.',
    'ends_with' => 'يجب أن ينتهي حقل :attribute بأحد الخيارات التالية: :values.',
    'exists' => 'حقل :attribute المختار غير صالح.',
    'file' => 'يجب أن يكون حقل :attribute ملفاً.',
    'filled' => 'يجب أن يحتوي حقل :attribute على قيمة.',
    'gt' => [
        'numeric' => 'يجب أن يكون حقل :attribute أكبر من :value.',
        'file' => 'يجب أن يكون حقل :attribute أكبر من :value كيلوبايت.',
        'string' => 'يجب أن يكون حقل :attribute أكثر من :value أحرف.',
        'array' => 'يجب أن يحتوي حقل :attribute على أكثر من :value عناصر.',
    ],
    'gte' => [
        'numeric' => 'يجب أن يكون حقل :attribute أكبر من أو مساوياً :value.',
        'file' => 'يجب أن يكون حقل :attribute أكبر من أو مساوياً :value كيلوبايت.',
        'string' => 'يجب أن يكون حقل :attribute أكثر من أو مساوياً :value أحرف.',
        'array' => 'يجب أن يحتوي حقل :attribute على :value عناصر أو أكثر.',
    ],
    'image' => 'يجب أن يكون حقل :attribute صورة.',
    'in' => 'حقل :attribute المختار غير صالح.',
    'in_array' => 'حقل :attribute غير موجود في :other.',
    'integer' => 'يجب أن يكون حقل :attribute عدداً صحيحاً.',
    'ip' => 'يجب أن يكون حقل :attribute عنوان IP صالحاً.',
    'ipv4' => 'يجب أن يكون حقل :attribute عنوان IPv4 صالحاً.',
    'ipv6' => 'يجب أن يكون حقل :attribute عنوان IPv6 صالحاً.',
    'json' => 'يجب أن يكون حقل :attribute نص JSON صالحاً.',
    'lt' => [
        'numeric' => 'يجب أن يكون حقل :attribute أقل من :value.',
        'file' => 'يجب أن يكون حقل :attribute أقل من :value كيلوبايت.',
        'string' => 'يجب أن يكون حقل :attribute أقل من :value أحرف.',
        'array' => 'يجب أن يحتوي حقل :attribute على أقل من :value عناصر.',
    ],
    'lte' => [
        'numeric' => 'يجب أن يكون حقل :attribute أقل من أو مساوياً :value.',
        'file' => 'يجب أن يكون حقل :attribute أقل من أو مساوياً :value كيلوبايت.',
        'string' => 'يجب أن يكون حقل :attribute أقل من أو مساوياً :value أحرف.',
        'array' => 'يجب ألا يحتوي حقل :attribute على أكثر من :value عناصر.',
    ],
    'max' => [
        'numeric' => 'قد لا يكون حقل :attribute أكبر من :max.',
        'file' => 'قد لا يكون حقل :attribute أكبر من :max كيلوبايت.',
        'string' => 'قد لا يكون حقل :attribute أكبر من :max أحرف.',
        'array' => 'قد لا يحتوي حقل :attribute على أكثر من :max عناصر.',
    ],
    'mimes' => 'يجب أن يكون حقل :attribute ملف من نوع: :values.',
    'mimetypes' => 'يجب أن يكون حقل :attribute ملف من نوع: :values.',
    'min' => [
        'numeric' => 'يجب أن يكون حقل :attribute على الأقل :min.',
        'file' => 'يجب أن يكون حقل :attribute على الأقل :min كيلوبايت.',
        'string' => 'يجب أن يكون حقل :attribute على الأقل :min أحرف.',
        'array' => 'يجب أن يحتوي حقل :attribute على الأقل :min عناصر.',
    ],
    'not_in' => 'حقل :attribute المختار غير صالح.',
    'not_regex' => 'صيغة حقل :attribute غير صالحة.',
    'numeric' => 'يجب أن يكون حقل :attribute رقماً.',
    'password' => 'كلمة المرور غير صحيحة.',
    'present' => 'يجب أن يكون حقل :attribute موجوداً.',
    'regex' => 'صيغة حقل :attribute غير صالحة.',
    'required' => 'حقل :attribute مطلوب.',
    'required_if' => 'حقل :attribute مطلوب عندما يكون :other هو :value.',
    'required_unless' => 'حقل :attribute مطلوب ما لم يكن :other في :values.',
    'required_with' => 'حقل :attribute مطلوب عندما يكون :values موجوداً.',
    'required_with_all' => 'حقل :attribute مطلوب عندما تكون :values موجودة.',
    'required_without' => 'حقل :attribute مطلوب عندما لا يكون :values موجوداً.',
    'required_without_all' => 'حقل :attribute مطلوب عندما لا تكون أي من :values موجودة.',
    'same' => 'يجب أن يطابق حقل :attribute و :other.',
    'size' => [
        'numeric' => 'يجب أن يكون حقل :attribute :size.',
        'file' => 'يجب أن يكون حقل :attribute :size كيلوبايت.',
        'string' => 'يجب أن يكون حقل :attribute :size أحرف.',
        'array' => 'يجب أن يحتوي حقل :attribute على :size عناصر.',
    ],
    'starts_with' => 'يجب أن يبدأ حقل :attribute بأحد الخيارات التالية: :values.',
    'string' => 'يجب أن يكون حقل :attribute نصاً.',
    'timezone' => 'يجب أن يكون حقل :attribute منطقة صالحة.',
    'unique' => 'قيمة حقل :attribute مستخدمة بالفعل.',
    'uploaded' => 'فشل تحميل حقل :attribute.',
    'url' => 'صيغة حقل :attribute غير صالحة.',
    'uuid' => 'يجب أن يكون حقل :attribute UUID صالحاً.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines.
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
    | The following language lines are used to swap attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email".
    |
    */

    'attributes' => [
        'name' => 'الاسم',
        'email' => 'البريد الإلكتروني',
        'mobile_number' => 'رقم الهاتف المحمول',
        'password' => 'كلمة المرور',
        'password_confirmation' => 'تأكيد كلمة المرور',
        'username' => 'اسم المستخدم',
        'current_password' => 'كلمة المرور الحالية',
        'otp' => 'رمز التحقق',
    ],

];
