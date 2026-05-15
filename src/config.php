<?php

use Straylightagency\LaravelAirTable\AirTableManager;

return [
    'api_key' => env('AIRTABLE_APIKEY', ''),
    'api_url' => env('AIRTABLE_API_URL', AirTableManager::API_URL ),
    'base_id' => env('AIRTABLE_BASE_ID', ''),
];