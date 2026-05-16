<?php

namespace Straylightagency\LaravelAirtable;

/**
 * Airtable Base.
 *
 * @package Straylightagency\LaravelAirtable
 * @author Anthony Pauwels <anthony@straylightagency.be>
 */
class Base
{
    /**
     * Base constructor.
     *
     * @param Client $client
     */
    public function __construct(
        protected Client $client
    ) {
    }

    /**
     * Get a builder for a table.
     *
     * @param string $table_name
     * @return Table
     */
    public function table(string $table_name): Table
    {
        return new Table( $this->client, $table_name );
    }
}
