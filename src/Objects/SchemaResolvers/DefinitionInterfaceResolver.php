<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Objects\SchemaResolvers;

use Assert\Assert;
use FlixTech\AvroSerializer\Objects\HasSchemaDefinitionInterface;
use FlixTech\AvroSerializer\Objects\SchemaResolverInterface;

class DefinitionInterfaceResolver implements SchemaResolverInterface
{
    /**
     * @throws \AvroSchemaParseException
     */
    public function valueSchemaFor(mixed $record): \AvroSchema
    {
        /** @var HasSchemaDefinitionInterface $record */
        $this->guardRecordHasDefinition($record);

        return \AvroSchema::parse($record::valueSchemaJson());
    }

    /**
     * @throws \AvroSchemaParseException
     */
    public function keySchemaFor(mixed $record): ?\AvroSchema
    {
        $this->guardRecordHasDefinition($record);

        $keySchemaJson = $record::keySchemaJson();

        if (!$keySchemaJson) {
            return null;
        }

        return \AvroSchema::parse($keySchemaJson);
    }

    private function guardRecordHasDefinition(mixed $record): void
    {
        Assert::that($record)
            ->isObject()
            ->implementsInterface(HasSchemaDefinitionInterface::class);
    }
}
