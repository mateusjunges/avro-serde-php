<?php

namespace FlixTech\AvroSerializer\Test\Objects\SchemaResolvers;

use FlixTech\AvroSerializer\Objects\HasSchemaDefinitionInterface;
use FlixTech\AvroSerializer\Objects\SchemaResolvers\DefinitionInterfaceResolver;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class DefinitionInterfaceResolverTest extends TestCase
{
    /**
     * @throws \AvroSchemaParseException
     */
    #[Test]
    public function it_should_allow_correct_interfaces(): void
    {
        $definitionInterfaceResolver = new DefinitionInterfaceResolver();
        $definitionClass = $this->createAnonymousDefinitionInterface();

        $this->assertEquals(
            \AvroSchema::parse('{"type": "string"}'),
            $definitionInterfaceResolver->valueSchemaFor($definitionClass)
        );

        $this->assertNull($definitionInterfaceResolver->keySchemaFor($definitionClass));

        $definitionClass = $this->createAnonymousDefinitionInterface(
            '{"type": "int"}'
        );

        $this->assertEquals(
            \AvroSchema::parse('{"type": "string"}'),
            $definitionInterfaceResolver->valueSchemaFor($definitionClass)
        );

        $this->assertEquals(
            \AvroSchema::parse('{"type": "int"}'),
            $definitionInterfaceResolver->keySchemaFor($definitionClass)
        );
    }

    /**
     * @throws \AvroSchemaParseException
     */
    #[Test]
    public function it_should_fail_for_records_not_implementing_the_interface_for_value_schema(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $definitionInterfaceResolver = new DefinitionInterfaceResolver();

        $definitionInterfaceResolver->valueSchemaFor([]);
    }

    /**
     * @throws \AvroSchemaParseException
     */
    #[Test]
    public function it_should_fail_for_records_not_implementing_the_interface_for_key_schema(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $definitionInterfaceResolver = new DefinitionInterfaceResolver();

        $definitionInterfaceResolver->keySchemaFor([]);
    }

    private function createAnonymousDefinitionInterface(?string $keySchema = null): HasSchemaDefinitionInterface
    {
        $class = new class implements HasSchemaDefinitionInterface {
            public static string $valueSchema;

            public static ?string $keySchema;

            public static function valueSchemaJson(): string
            {
                return self::$valueSchema;
            }

            public static function keySchemaJson(): ?string
            {
                return self::$keySchema;
            }
        };

        $class::$valueSchema = '{"type": "string"}';
        $class::$keySchema = $keySchema;

        return $class;
    }
}
