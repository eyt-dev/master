<?php

return [
    // Authentication & Authorization
    'auth' => [
        'unauthorized' => 'غير مصرح',
        'unauthenticated' => 'غير مصرح',
        'user_not_authenticated' => 'المستخدم غير مصرح.',
        'invalid_credentials' => 'بيانات اعتماد غير صحيحة.',
        'login_successful' => 'تم تسجيل الدخول بنجاح.',
        'signup_successful' => 'تم التسجيل بنجاح.',
        'logout_successful' => 'تم تسجيل الخروج بنجاح.',
        'password_reset_successful' => 'تم إعادة تعيين كلمة المرور بنجاح.',
        'password_changed_successful' => 'تم تغيير كلمة المرور بنجاح.',
        'current_password_incorrect' => 'كلمة المرور الحالية غير صحيحة.',
    ],

    // Profile
    'profile' => [
        'profile_retrieved' => 'تم استرجاع الملف الشخصي بنجاح.',
        'profile_updated' => 'تم تحديث الملف الشخصي بنجاح.',
        'profile_not_found' => 'لم يتم العثور على الملف الشخصي.',
    ],

    // Generic CRUD
    'crud' => [
        'created_successfully' => 'تم إنشاء :resource بنجاح.',
        'updated_successfully' => 'تم تحديث :resource بنجاح.',
        'deleted_successfully' => 'تم حذف :resource بنجاح.',
        'retrieved_successfully' => 'تم استرجاع :resource بنجاح.',
        'list_retrieved_successfully' => 'تم استرجاع قائمة :resource بنجاح.',
        'not_found' => 'لم يتم العثور على :resource.',
    ],

    // Validation & Errors
    'validation' => [
        'invalid_input' => 'إدخال غير صحيح.',
        'field_required' => 'هذا الحقل مطلوب.',
        'already_exists' => ':resource موجود بالفعل.',
        'operation_failed' => 'فشلت العملية. يرجى المحاولة مرة أخرى.',
        'unauthorized_action' => 'أنت غير مصرح بإجراء هذا الإجراء.',
    ],

    // Resources
    'resources' => [
        'Unit' => 'الوحدة',
        'Element' => 'العنصر',
        'Form' => 'النموذج',
        'Component' => 'المكون',
        'Formulation' => 'الصيغة',
        'CompoPrice' => 'سعر المكون',
        'User' => 'المستخدم',
        'Profile' => 'الملف الشخصي',
    ],

    // Status & Labels
    'status' => [
        'active' => 'نشط',
        'inactive' => 'غير نشط',
        'pending' => 'قيد الانتظار',
        'completed' => 'مكتمل',
        'failed' => 'فشل',
    ],
];
