<?php

namespace Straylightagency\LaravelAirtable;

/**
 * AirtableManager
 *
 * @package Straylightagency\LaravelAirtable
 * @author Anthony Pauwels <anthony@straylightagency.be>
 */
class AirtableManager
{
    /** @var string */
    const string API_URL = 'https://api.airtable.com/v0/%s/';

    /** @var Base[] */
    protected array $bases = [];

    /**
     * AirtableManager constructor.
     *
     * @param string $apiKey
     * @param string $baseId
     * @param string $apiUrl
     */
    public function __construct(
        protected string $apiKey,
        protected string $baseId,
        protected string $apiUrl = self::API_URL
    ) {
    }

    /**
     * Get a builder for a table from the default base
     *
     * @param string $table_name
     * @return Table
     */
    public function table(string $table_name): Table
    {
        return $this->on( $this->baseId )->table( $table_name );
    }

    /**
     * Get a base
     *
     * @param string $base_id
     * @return Base
     */
    public function on(string $base_id): Base
    {
        if ( isset( $this->bases[ $base_id ] ) ) {
            return $this->bases[ $base_id ];
        }

        return $this->bases[ $base_id ] = new Base(
            new Client(
                baseUrl: sprintf( $this->apiUrl, $base_id ),
                apiKey: $this->apiKey
            )
        );
    }
}
