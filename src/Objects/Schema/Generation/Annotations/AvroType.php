<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Objects\Schema\Generation\Annotations;

use FlixTech\AvroSerializer\Objects\Schema\AttributeName;
use FlixTech\AvroSerializer\Objects\Schema\Generation\SchemaAttribute;
use FlixTech\AvroSerializer\Objects\Schema\Generation\SchemaAttributes;

/**
 * @Annotation
 */
final class AvroType implements SchemaAttribute
{
    public string $value;

    /**
     * @var array<\FlixTech\AvroSerializer\Objects\Schema\Generation\SchemaAttribute>
     */
    public array $attributes = [];

    public static function create(string $typeName, SchemaAttribute ...$attributes): self
    {
        $avroType = new self();

        $avroType->value = $typeName;
        $avroType->attributes = $attributes;

        return $avroType;
    }

    public function name(): string
    {
        return AttributeName::TYPE;
    }

    public function value(): string
    {
        return $this->value;
    }

    public function attributes(): SchemaAttributes
    {
        return new SchemaAttributes(...$this->attributes);
    }
}
