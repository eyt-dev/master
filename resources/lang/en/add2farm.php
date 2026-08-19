<?php

return [
    // Messages
    'messages' => [
        'otp_sent_successfully' => 'OTP sent successfully.',
        'registration_successful' => 'Registration successful.',
        'otp_verified_successfully' => 'OTP verified successfully.',
        'otp_verified_password_reset' => 'OTP verified successfully. You can now reset your password.',
        'invalid_credentials' => 'Invalid credentials.',
        'account_disabled' => 'Your account has been disabled.',
        'user_not_found' => 'User not found.',
        'no_otp_found' => 'No OTP found. Please request OTP first.',
        'otp_expired' => 'OTP has expired. Please request a new OTP.',
        'invalid_otp' => 'Invalid OTP.',
        'logged_out_successfully' => 'Logged out successfully.',
        'user_not_authenticated' => 'User not authenticated.',
        'invalid_or_expired_token' => 'Invalid or expired token. Please login again.',
        'registration_failed' => 'Registration failed.',
        'otp_verification_failed' => 'OTP verification failed.',
        'failed_to_resend_otp' => 'Failed to resend OTP.',
        'failed_to_send_otp' => 'Failed to send OTP.',
        'invalid_or_expired_token_otp' => 'Invalid or expired token. Please verify OTP first.',
        'invalid_token_use_otp' => 'Invalid token. Please use token from OTP verification.',
        'password_reset_successfully' => 'Password reset successfully. Please login with new password.',
        'password_reset_failed' => 'Password reset failed.',
        'profile_updated_successfully' => 'Profile updated successfully.',
        'password_changed_successfully' => 'Password changed successfully.',
        'current_password_incorrect' => 'Current password is incorrect.',
        'supervisor_created_successfully' => 'Supervisor created successfully.',
        'supervisor_updated_successfully' => 'Supervisor updated successfully.',
        'supervisor_deleted_successfully' => 'Supervisor deleted successfully.',
        'supervisor_not_found' => 'Supervisor not found.',
        'farmer_created_successfully' => 'Farmer created successfully.',
        'farmer_updated_successfully' => 'Farmer updated successfully.',
        'farmer_deleted_successfully' => 'Farmer deleted successfully.',
        'farmer_not_found' => 'Farmer not found.',
        'farm_created_successfully' => 'Farm created successfully.',
        'farm_updated_successfully' => 'Farm updated successfully.',
        'farm_deleted_successfully' => 'Farm deleted successfully.',
        'farm_not_found' => 'Farm not found.',
        'flock_created_successfully' => 'Flock created successfully.',
        'flock_updated_successfully' => 'Flock updated successfully.',
        'flock_deleted_successfully' => 'Flock deleted successfully.',
        'flock_not_found' => 'Flock not found.',
        'daily_record_created_successfully' => 'Daily record created successfully.',
        'daily_record_updated_successfully' => 'Daily record updated successfully.',
        'daily_record_deleted_successfully' => 'Daily record deleted successfully.',
        'daily_record_not_found' => 'Daily record not found.',
        'unauthorized_action' => 'You are not authorized to perform this action.',
        'operation_failed' => 'Operation failed. Please try again.',
    ],

    // Type labels
    'user_types' => [
        1 => 'Farm Admin',
        2 => 'Farm Owner',
        3 => 'Supervisor',
        4 => 'Farmer',
    ],

    // Status labels
    'status' => [
        'active' => 'Active',
        'inactive' => 'Inactive',
        'disabled' => 'Disabled',
    ],

    // Field labels
    'fields' => [
        'mobile_number' => 'Mobile Number',
        'password' => 'Password',
        'password_confirmation' => 'Password Confirmation',
        'name' => 'Name',
        'email' => 'Email',
        'username' => 'Username',
        'current_password' => 'Current Password',
        'otp' => 'OTP',
        'type' => 'Type',
        'status' => 'Status',
    ],

    // Validation messages
    'validation' => [
        'mobile_unique' => 'The mobile number has already been taken.',
        'email_unique' => 'The email has already been taken.',
        'username_unique' => 'The username has already been taken.',
    ],
];
