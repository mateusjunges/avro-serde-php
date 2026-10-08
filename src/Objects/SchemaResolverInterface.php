<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Objects;

/**
 * Resolves value and/or key schemas for a given record.
 */
interface SchemaResolverInterface
{
    public function valueSchemaFor(mixed $record): \AvroSchema;

    /**
     * This method should resolve the Avro key schema for a given record.
     *
     * The method should return `NULL` *only* when the record is not supposed to have a key schema.
     * If the key schema cannot be resolved otherwise, this method should throw an `CannotResolveSchemaException`.
     */
    public function keySchemaFor(mixed $record): ?\AvroSchema;
}
