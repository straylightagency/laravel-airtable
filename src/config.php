<?php

use Straylightagency\LaravelAirtable\AirtableManager;

return [
    'api_key' => env('AIRTABLE_APIKEY', ''),
    'api_url' => env('AIRTABLE_API_URL', AirtableManager::API_URL ),
    'base_id' => env('AIRTABLE_BASE_ID', ''),
];