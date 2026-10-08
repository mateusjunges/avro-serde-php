<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Objects\Schema\Record;

class FieldDefault extends FieldOption
{
    public function __construct(mixed $default)
    {
        parent::__construct('default', $default);
    }
}
