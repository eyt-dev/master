<?php

return [
    // Authentication & Authorization
    'auth' => [
        'unauthorized' => 'Unauthorized',
        'unauthenticated' => 'Unauthenticated',
        'user_not_authenticated' => 'User not authenticated.',
        'invalid_credentials' => 'Invalid credentials.',
        'login_successful' => 'Login successful.',
        'signup_successful' => 'Signup successful.',
        'logout_successful' => 'Logged out successfully.',
        'password_reset_successful' => 'Password reset successfully.',
        'password_changed_successful' => 'Password changed successfully.',
        'current_password_incorrect' => 'Current password is incorrect.',
    ],

    // Profile
    'profile' => [
        'profile_retrieved' => 'Profile retrieved successfully.',
        'profile_updated' => 'Profile updated successfully.',
        'profile_not_found' => 'Profile not found.',
    ],

    // Generic CRUD
    'crud' => [
        'created_successfully' => ':resource created successfully.',
        'updated_successfully' => ':resource updated successfully.',
        'deleted_successfully' => ':resource deleted successfully.',
        'retrieved_successfully' => ':resource retrieved successfully.',
        'list_retrieved_successfully' => ':resource list retrieved successfully.',
        'not_found' => ':resource not found.',
    ],

    // Validation & Errors
    'validation' => [
        'invalid_input' => 'Invalid input.',
        'field_required' => 'This field is required.',
        'already_exists' => ':resource already exists.',
        'operation_failed' => 'Operation failed. Please try again.',
        'unauthorized_action' => 'You are not authorized to perform this action.',
    ],

    // Resources
    'resources' => [
        'Unit' => 'Unit',
        'Element' => 'Element',
        'Form' => 'Form',
        'Component' => 'Component',
        'Formulation' => 'Formulation',
        'CompoPrice' => 'Component Price',
        'User' => 'User',
        'Profile' => 'Profile',
    ],

    // Status & Labels
    'status' => [
        'active' => 'Active',
        'inactive' => 'Inactive',
        'pending' => 'Pending',
        'completed' => 'Completed',
        'failed' => 'Failed',
    ],
];
