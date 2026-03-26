<?php

return [
    // Fixed sender for recruitment emails.
    'sender_email' => env('RECRUITMENT_IMAP_USERNAME'),

    // Optional CC recipient for retained-candidate emails.
    'cc_email' => env('RECRUITMENT_CC_EMAIL', 'info@ya-consulting.com'),

    // Dedicated SMTP settings for sending recruitment emails.
    'smtp' => [
        'host' => env('RECRUITMENT_SMTP_HOST', env('RECRUITMENT_IMAP_HOST', env('MAIL_HOST'))),
        'port' => (int) env('RECRUITMENT_SMTP_PORT', 465),
        'encryption' => env('RECRUITMENT_SMTP_ENCRYPTION', 'ssl'),
        'username' => env('RECRUITMENT_IMAP_USERNAME'),
        'password' => env('RECRUITMENT_IMAP_PASSWORD'),
        'from_name' => env('RECRUITMENT_FROM_NAME', env('MAIL_FROM_NAME', config('app.name', 'ManageX'))),
    ],
];

