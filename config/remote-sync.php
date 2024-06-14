<?php
return [
    '*' => [
        // Enable pull and restore functionality
        'disableRestore' => false,
    ],
    'dev' => [],
    'staging' => [],
    'production' => [
        // Disable pull and restore only in production environment
        'disableRestore' => true,
    ],
];
