<?php

/**
 * -----------------------------------------------------------------------------
 * Generated 2014-12-18T19:51:35+00:00.
 *
 * @item      files.allowed_types
 * @group     conversations
 * @namespace null
 * -----------------------------------------------------------------------------
 */
return [
    'attachments_enabled' => true,
    'attachments_pending_file_set' => 'Conversation Messages (Pending)',
    'attachments_file_set' => 'Conversation Messages',
    'subscription_enabled' => false,
    'files' => [
        'allowed_types' => '*.jpg;*.gif;*.jpeg;*.png;*.doc;*.docx;*.zip',
        // Black list of semi-colon separated list of blacklisted file extensions (takes the precedence over allowed_types)
        // Set to NULL to use the value of the concrete.upload.extensions_denylist configuration key
        'disallowed_types' => null,
        'guest' => [
            'max_size' => 1,
            'max' => 3,
        ],
        'registered' => [
            'max_size' => 10,
            'max' => 5,
        ],
    ],
];
