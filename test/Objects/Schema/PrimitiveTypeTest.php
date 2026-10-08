<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Test\Objects\Schema;

use FlixTech\AvroSerializer\Objects\Schema;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class PrimitiveTypeTest extends TestCase
{
    #[Test]
    #[DataProvider('providePrimitiveTypes')]
    public function it_should_serialize_primitive_types(Schema $type, string $expectedName): void
    {
        $this->assertEquals($expectedName, $type->serialize());
    }

    #[Test]
    #[DataProvider('providePrimitiveTypes')]
    public function it_should_parse_primitive_types(Schema $type, string $expectedName): void
    {
        $parsedSchema = $type->parse();
        $this->assertEquals($expectedName, $parsedSchema->type());
    }

    public static function providePrimitiveTypes(): array
    {
        return [
            'null' => [Schema::null(), 'null'],
            'boolean' => [Schema::boolean(), 'boolean'],
            'int' => [Schema::int(), 'int'],
            'long' => [Schema::long(), 'long'],
            'float' => [Schema::float(), 'float'],
            'double' => [Schema::double(), 'double'],
            'bytes' => [Schema::bytes(), 'bytes'],
            'string' => [Schema::string(), 'string'],
        ];
    }
}
