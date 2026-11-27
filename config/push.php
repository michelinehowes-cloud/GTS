<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Push notifications to the Android app (Firebase Cloud Messaging, HTTP v1)
    |--------------------------------------------------------------------------
    | Push stays OFF until the Firebase service-account file exists.
    |
    | Setup (once):
    |   1. Firebase console -> create a project -> add an Android app with package "com.graduate.training".
    |   2. Download google-services.json -> put it in android/app/ and rebuild the app.
    |   3. Project settings -> Service accounts -> "Generate new private key" -> save the JSON on the server as
    |      storage/app/firebase-service-account.json (never commit it).
    */
    'enabled' => env('PUSH_ENABLED', true),

    'credentials' => env('FCM_CREDENTIALS', storage_path('app/firebase-service-account.json')),

    // Usually read from the credentials file; set only to override.
    'project_id' => env('FCM_PROJECT_ID'),

    // Max parallel requests to Firebase when notifying many phones
    'concurrency' => 20,
];
