<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Objects\Schema\Record;

abstract class FieldOption
{
    private string $name;
    private mixed $value;

    public function __construct(string $name, mixed $value)
    {
        $this->name = $name;
        $this->value = $value;
    }

    public static function doc(string $doc): FieldDoc
    {
        return new FieldDoc($doc);
    }

    public static function default(mixed $default): FieldDefault
    {
        return new FieldDefault($default);
    }

    public static function orderAsc(): FieldOrder
    {
        return FieldOrder::asc();
    }

    public static function orderDesc(): FieldOrder
    {
        return FieldOrder::desc();
    }

    public static function orderIgnore(): FieldOrder
    {
        return FieldOrder::ignore();
    }

    public static function aliases(string $alias, string ...$other): FieldAliases
    {
        return new FieldAliases($alias, ...$other);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }
}
