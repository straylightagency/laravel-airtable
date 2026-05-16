<?php

namespace Straylightagency\LaravelAirtable;

use RuntimeException;
use Illuminate\Support\Arr;
use Illuminate\Http\Client\ConnectionException;

/**
 * Airtable Table query builder.
 *
 * @package Straylightagency\LaravelAirtable
 * @author Anthony Pauwels <anthony@straylightagency.be>
 */
class Table
{
    /** @var bool */
    protected bool $typecast = false;

    /** @var integer */
    protected int $delay = 200000;

    /** @var array */
    protected array $fields = [];

    /** @var string */
    protected string $criteria;

    /** @var string */
    protected string $view;

    /** @var integer */
    protected int $limit;

    /** @var integer */
    protected int $offset;

    /** @var array */
    protected array $sort = [];

    /** @var array */
    protected array $operators = [
        '>', '<', '>=', '<=', '=', '!='
    ];

    /**
     * Table constructor.
     *
     * @param Client $client
     * @param string $tableName
     */
    public function __construct(
        protected Client $client,
        protected string $tableName
    ) {
    }

    /**
     * Count the number of elements inside the query.
     *
     * @throws RuntimeException
     * @throws ConnectionException
     */
    public function count(): int
    {
        return count( $this->get() );
    }

    /**
     * If Airtable must perform an automatic data conversion from string values.
     *
     * @param bool $value
     * @return $this
     */
    public function typecast(bool $value = true): self
    {
        $this->typecast = $value;

        return $this;
    }

    /**
     * Delay between request.
     *
     * @param int $value
     * @return $this
     */
    public function delay(int $value): self
    {
        $this->delay = $value;

        return $this;
    }

    /**
     * Search for specific fields from records
     *
     * @param array|string $fields
     * @return $this
     */
    public function fields(array|string $fields): self
    {
        $this->fields = array_merge( $this->fields, Arr::wrap( $fields ) );

        return $this;
    }

    /**
     * Filter records using a logical where operation.
     *
     * @param string $field
     * @param mixed $operator
     * @param null $value
     * @return $this
     */
    public function where(string $field, mixed $operator, $value = null): self
    {
        if ( ! $this->invalidOperatorAndValue( $operator, $value ) ) {
            $value = $operator;
            $operator = '=';
        }

        $this->criteria = '{' . $field . '}' . $operator . '"' . $value . '"';

        return $this;
    }

    /**
     * Filter records using a raw query.
     *
     * @param string $formula
     * @return $this
     */
    public function whereRaw(string $formula): self
    {
        $this->criteria = $formula;

        return $this;
    }

    /**
     * Determine if $operator is the where value or not.
     *
     * @param string $operator
     * @param mixed $value
     * @return bool
     */
    protected function invalidOperatorAndValue(string $operator, mixed $value): bool
    {
        return is_null( $value ) && in_array( $operator, $this->operators ) && ! in_array( $operator, [ '=', '!=' ] );
    }

    /**
     * Get records from a specific view.
     *
     * @param string $view_name
     * @return $this
     */
    public function view(string $view_name): self
    {
        $this->view = $view_name;

        return $this;
    }

    /**
     * Order records by a field and direction.
     *
     * @param string $field
     * @param string $direction
     * @return $this
     */
    public function orderBy(string $field, string $direction = 'asc'): self
    {
        $this->sort[] = compact('field', 'direction');

        return $this;
    }

    /**
     * Set the limit value to get a limited number of records.
     *
     * @param int $value
     * @return $this
     */
    public function limit(int $value): self
    {
        $this->limit = $value;

        return $this;
    }

    /**
     * Alias to limit method.
     *
     * @param int $value
     * @return $this
     */
    public function take(int $value): self
    {
        return $this->limit( $value );
    }

    /**
     * Set the offset value to get records from a specific page.
     *
     * @param int $value
     * @return $this
     */
    public function offset(int $value): self
    {
        $this->offset = $value;

        return $this;
    }

    /**
     * Alias to offset method.
     *
     * @param int $value
     * @return $this
     */
    public function skip(int $value): self
    {
        return $this->offset( $value );
    }

    /**
     * Get records with a limit of 100 by page.
     *
     * @return array
     *
     * @throws RuntimeException
     * @throws ConnectionException
     */
    public function get(): array
    {
        $records = [];

        do  {
            $params = [];

            if ( !empty( $this->criteria ) ) {
                $params['filterByFormula'] = $this->criteria;
            }

            if ( !empty( $this->view ) ) {
                $params['view'] = $this->view;
            }

            if ( !empty( $this->sort ) ) {
                $params['sort'] = $this->sort;
            }

            if ( !empty( $this->limit ) ) {
                $params['pageSize'] = $this->limit;
            }

            if ( !empty( $this->offset ) ) {
                $params['offset'] = $this->offset;
            }

            $response = $this->client->sendGet( $this->tableName, $params );

            if ( isset( $response['records'] ) ) {
                $records += $response['records'];
            }

            if ( isset( $response['offset'] ) ) {
                $this->offset = $response['offset'];

                usleep( $this->delay );
            } else {
                $this->offset = false;
            }

        } while( $this->offset );

        foreach ( $records as $index => $record ) {
            $records[ $index ] = $this->formatRecord( $record );
        }

        return $records;
    }

    /**
     * Method alias to get, return all records.
     *
     * @return array
     *
     * @throws RuntimeException
     * @throws ConnectionException
     */
    public function all(): array
    {
        return $this->get();
    }

    /**
     * Get the first record.
     *
     * @return array
     *
     * @throws RuntimeException
     * @throws ConnectionException
     */
    public function first(): array
    {
        $records = $this->get();

        return $this->formatRecord( array_shift( $records ) );
    }

    /**
     * Find a record using his ID.
     *
     * @param string $id
     * @return array
     *
     * @throws RuntimeException
     * @throws ConnectionException
     */
    public function find(string $id): array
    {
        $record = $this->client->sendGet( $this->tableName . '/' . $id );

        return $this->formatRecord( $record );
    }

    /**
     * Insert a record.
     *
     * @param array $data
     * @return array
     *
     * @throws RuntimeException
     * @throws ConnectionException
     */
    public function insert(array $data): array
    {
        $record = $this->client->sendPost( $this->tableName, [
            'json' => [
                'fields' => (object) $data,
                'typecast' => $this->typecast,
            ]
        ] );

        return $this->formatRecord( $record );
    }

    /**
     * Update a record or many records. Destructive way.
     *
     * @param array|string $id
     * @param array|null $data
     * @return array
     *
     * @throws RuntimeException
     * @throws ConnectionException
     */
    public function update(array|string $id, array $data = null): array
    {
        if ( is_array( $id ) && $data === null ) {
            return $this->performMassUpdate( $id, 'sendPut' );
        }

        $record = $this->client->sendPut( $this->tableName . '/' . $id, [
            'json' => [
                'fields' => (object) $data,
                'typecast' => $this->typecast,
            ]
        ] );

        return $this->formatRecord( $record );
    }

    /**
     * Patch a single record or many records.
     *
     * @param array|string $id
     * @param array|null $data
     * @return array
     *
     * @throws RuntimeException
     * @throws ConnectionException
     */
    public function patch(array|string $id, array $data = null): array
    {
        if ( is_array( $id ) && $data === null ) {
            return $this->performMassUpdate( $id, 'sendPath' );
        }

        $record = $this->client->sendPatch( $this->tableName . '/' . $id, [
            'json' => [
                'fields' => (object) $data,
                'typecast' => $this->typecast,
            ]
        ] );

        return $this->formatRecord( $record );
    }

    /**
     * Delete a single record.
     *
     * @param string $id
     * @return array
     *
     * @throws RuntimeException
     * @throws ConnectionException
     */
    public function delete(string $id): array
    {
        return $this->client->sendDelete( $this->tableName . '/' . $id );
    }

    /**
     * Perform a mass update using sendPut or sendPath method.
     *
     * @param array $data
     * @param string $method
     * @return array
     */
    protected function performMassUpdate(array $data, string $method): array
    {
        if ( ! in_array( $method, ['sendPut', 'sendPath'] ) ) {
            return [];
        }

        $records = [];
        $chunks = array_chunk( $data, 10 );

        foreach ( $chunks as $key => $item ) {
            $params =  [
                'json' => [
                    'fields' => (object) $item,
                    'typecast' => $this->typecast,
                ]
            ];

            $response = $this->client->$method( $this->tableName, $params );
            $records += $response['records'];

            if ( isset( $chunks[ $key + 1 ] ) ) {
                usleep( $this->delay );
            }
        }

        foreach ( $records as $index => $record ) {
            $records[ $index ] = $this->formatRecord( $record );
        }

        return $records;
    }

    /**
     * Format the Record object to get "id" and "createdTime" in the same level as "fields".
     *
     * @param array $record
     * @return array
     */
    protected function formatRecord(array $record): array
    {
        if ( isset( $record['fields'] ) ) {
            return array_merge( $record['fields'], ['createdTime' => $record['createdTime'] ], ['id' => $record['id'] ] );
        }

        return $record;
    }
}
