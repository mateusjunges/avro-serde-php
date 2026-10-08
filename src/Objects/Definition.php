<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Objects;

interface Definition
{
    public function serialize(): mixed;
}
