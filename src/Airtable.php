<?php

namespace Straylightagency\LaravelAirtable;

use Illuminate\Support\Facades\Facade;

/**
 * Facade.
 * Provide quick access methods to the AirtableManager class.
 *
 * @method static Table table(string $table_name)
 * @method static Base on(string $base_id)
 *
 * @package Straylightagency\LaravelAirtable
 * @author Anthony Pauwels <anthony@straylightagency.be>
 */
class Airtable extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor():string
    {
        return 'airtable';
    }
}
