<?php

use Straylightagency\LaravelAirtable\AirtableManager;

return [
    'base_id' => env('AIRTABLE_BASE_ID', ''),
    'token' => env('AIRTABLE_TOKEN', ''),
    'api_url' => env('AIRTABLE_API_URL', AirtableManager::API_URL ),
];