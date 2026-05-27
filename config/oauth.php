<?php

return [
    'client_id' => $_ENV['GOOGLE_CLIENT_ID'] ?? getenv('GOOGLE_CLIENT_ID') ?: '',
    'client_secret' => $_ENV['GOOGLE_CLIENT_SECRET'] ?? getenv('GOOGLE_CLIENT_SECRET') ?: '',
    'redirect_uri' => $_ENV['GOOGLE_REDIRECT_URI'] ?? getenv('GOOGLE_REDIRECT_URI') ?: 'http://localhost:8000/auth/google-callback.php',
];
