<?php

return [
    // Messages
    'messages' => [
        'otp_sent_successfully' => 'تم إرسال رمز التحقق بنجاح.',
        'registration_successful' => 'تم التسجيل بنجاح.',
        'otp_verified_successfully' => 'تم التحقق من رمز التحقق بنجاح.',
        'otp_verified_password_reset' => 'تم التحقق من رمز التحقق بنجاح. يمكنك الآن إعادة تعيين كلمة المرور.',
        'invalid_credentials' => 'بيانات اعتماد غير صحيحة.',
        'account_disabled' => 'تم تعطيل حسابك.',
        'user_not_found' => 'لم يتم العثور على المستخدم.',
        'no_otp_found' => 'لم يتم العثور على رمز التحقق. يرجى طلب رمز التحقق أولاً.',
        'otp_expired' => 'انتهت صلاحية رمز التحقق. يرجى طلب رمز التحقق الجديد.',
        'invalid_otp' => 'رمز التحقق غير صحيح.',
        'logged_out_successfully' => 'تم تسجيل الخروج بنجاح.',
        'user_not_authenticated' => 'المستخدم غير مصرح.',
        'invalid_or_expired_token' => 'الرمز غير صحيح أو انتهت صلاحيته. يرجى تسجيل الدخول مرة أخرى.',
        'registration_failed' => 'فشل التسجيل.',
        'otp_verification_failed' => 'فشل التحقق من رمز التحقق.',
        'failed_to_resend_otp' => 'فشل إرسال رمز التحقق.',
        'failed_to_send_otp' => 'فشل إرسال رمز التحقق.',
        'invalid_or_expired_token_otp' => 'الرمز غير صحيح أو انتهت صلاحيته. يرجى التحقق من رمز التحقق أولاً.',
        'invalid_token_use_otp' => 'رمز غير صحيح. يرجى استخدام الرمز من التحقق من رمز التحقق.',
        'password_reset_successfully' => 'تم إعادة تعيين كلمة المرور بنجاح. يرجى تسجيل الدخول باستخدام كلمة المرور الجديدة.',
        'password_reset_failed' => 'فشل إعادة تعيين كلمة المرور.',
        'profile_updated_successfully' => 'تم تحديث الملف الشخصي بنجاح.',
        'password_changed_successfully' => 'تم تغيير كلمة المرور بنجاح.',
        'current_password_incorrect' => 'كلمة المرور الحالية غير صحيحة.',
        'supervisor_created_successfully' => 'تم إنشاء المشرف بنجاح.',
        'supervisor_updated_successfully' => 'تم تحديث المشرف بنجاح.',
        'supervisor_deleted_successfully' => 'تم حذف المشرف بنجاح.',
        'supervisor_not_found' => 'لم يتم العثور على المشرف.',
        'farmer_created_successfully' => 'تم إنشاء المزارع بنجاح.',
        'farmer_updated_successfully' => 'تم تحديث المزارع بنجاح.',
        'farmer_deleted_successfully' => 'تم حذف المزارع بنجاح.',
        'farmer_not_found' => 'لم يتم العثور على المزارع.',
        'farm_created_successfully' => 'تم إنشاء المزرعة بنجاح.',
        'farm_updated_successfully' => 'تم تحديث المزرعة بنجاح.',
        'farm_deleted_successfully' => 'تم حذف المزرعة بنجاح.',
        'farm_not_found' => 'لم يتم العثور على المزرعة.',
        'flock_created_successfully' => 'تم إنشاء القطيع بنجاح.',
        'flock_updated_successfully' => 'تم تحديث القطيع بنجاح.',
        'flock_deleted_successfully' => 'تم حذف القطيع بنجاح.',
        'flock_not_found' => 'لم يتم العثور على القطيع.',
        'daily_record_created_successfully' => 'تم إنشاء السجل اليومي بنجاح.',
        'daily_record_updated_successfully' => 'تم تحديث السجل اليومي بنجاح.',
        'daily_record_deleted_successfully' => 'تم حذف السجل اليومي بنجاح.',
        'daily_record_not_found' => 'لم يتم العثور على السجل اليومي.',
        'unauthorized_action' => 'أنت غير مصرح بإجراء هذا الإجراء.',
        'operation_failed' => 'فشلت العملية. يرجى المحاولة مرة أخرى.',
    ],

    // Type labels
    'user_types' => [
        1 => 'مسؤول المزرعة',
        2 => 'مالك المزرعة',
        3 => 'المشرف',
        4 => 'المزارع',
    ],

    // Status labels
    'status' => [
        'active' => 'نشط',
        'inactive' => 'غير نشط',
        'disabled' => 'معطل',
    ],

    // Field labels
    'fields' => [
        'mobile_number' => 'رقم الهاتف المحمول',
        'password' => 'كلمة المرور',
        'password_confirmation' => 'تأكيد كلمة المرور',
        'name' => 'الاسم',
        'email' => 'البريد الإلكتروني',
        'username' => 'اسم المستخدم',
        'current_password' => 'كلمة المرور الحالية',
        'otp' => 'رمز التحقق',
        'type' => 'النوع',
        'status' => 'الحالة',
    ],

    // Validation messages
    'validation' => [
        'mobile_unique' => 'رقم الهاتف المحمول مستخدم بالفعل.',
        'email_unique' => 'البريد الإلكتروني مستخدم بالفعل.',
        'username_unique' => 'اسم المستخدم مستخدم بالفعل.',
    ],
];
