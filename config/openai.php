<?php

return [
    'api_key' => env('OPENAI_API_KEY'),
    'organization' => env('OPENAI_ORGANIZATION'),
    'base_uri' => env('OPENAI_API_BASE', 'https://api.openai.com/v1'),
    'request_timeout' => 30,
];
