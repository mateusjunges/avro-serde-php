<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Objects\Schema\Generation;

interface SchemaAttribute
{
    public function name(): string;

    public function value(): mixed;

    public function attributes(): SchemaAttributes;
}
