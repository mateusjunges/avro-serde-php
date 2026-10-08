<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Objects\SchemaResolvers;

use FlixTech\AvroSerializer\Objects\SchemaResolverInterface;

class CallableResolver implements SchemaResolverInterface
{
    /**
     * @var callable
     */
    private $valueSchemaResolverCallable;

    /**
     * @var callable|null
     */
    private $keySchemaResolverCallable;

    public function __construct(callable $valueSchemaResolverCallable, ?callable $keySchemaResolverCallable = null)
    {
        $this->valueSchemaResolverCallable = $valueSchemaResolverCallable;
        $this->keySchemaResolverCallable = $keySchemaResolverCallable;
    }

    /**
     * @throws \AvroSchemaParseException
     */
    public function valueSchemaFor(mixed $record): \AvroSchema
    {
        return \AvroSchema::parse(\call_user_func($this->valueSchemaResolverCallable, $record));
    }

    /**
     * @throws \AvroSchemaParseException
     */
    public function keySchemaFor(mixed $record): ?\AvroSchema
    {
        if (!$this->keySchemaResolverCallable) {
            return null;
        }

        return \AvroSchema::parse(\call_user_func($this->keySchemaResolverCallable, $record));
    }
}
