<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Integrations\Symfony\Serializer\NameConverter;

use FlixTech\AvroSerializer\Integrations\Symfony\Serializer\AvroSerDeEncoder;
use FlixTech\AvroSerializer\Objects\Schema\AttributeName;
use FlixTech\AvroSerializer\Objects\Schema\Generation\SchemaAttributeReader;
use Symfony\Component\Serializer\NameConverter\AdvancedNameConverterInterface;

class AvroNameConverter implements AdvancedNameConverterInterface
{
    private SchemaAttributeReader $attributeReader;

    /**
     * @var array<string, PropertyNameMap>
     */
    private array $mapCache = [];

    public function __construct(SchemaAttributeReader $attributeReader)
    {
        $this->attributeReader = $attributeReader;
    }

    /**
     * @throws \ReflectionException
     */
    public function normalize(
        string $propertyName,
        string $class = null,
        string $format = null,
        array $context = []
    ): string {
        return $this
            ->getNameMap($class, $format)
            ->getNormalized($propertyName);
    }

    /**
     * @throws \ReflectionException
     */
    private function getNameMap(?string $class, ?string $format): PropertyNameMap
    {
        if (null === $class || !class_exists($class)) {
            return new PropertyNameMap();
        }

        if (AvroSerDeEncoder::FORMAT_AVRO !== $format) {
            return new PropertyNameMap();
        }

        return $this->generateMap($class);
    }

    /**
     * @throws \ReflectionException
     */
    private function generateMap(string $class): PropertyNameMap
    {
        if (isset($this->mapCache[$class])) {
            return $this->mapCache[$class];
        }

        $reflectionClass = new \ReflectionClass($class);

        $map = array_reduce(
            $reflectionClass->getProperties(),
            [$this, 'propertyToSchemaName'],
            new PropertyNameMap()
        );

        $this->mapCache[$class] = $map;

        return $map;
    }

    private function propertyToSchemaName(
        PropertyNameMap $map,
        \ReflectionProperty $reflectionProperty
    ): PropertyNameMap {
        $schemaAttributes = $this->attributeReader->readPropertyAttributes($reflectionProperty);

        if (!$schemaAttributes->has(AttributeName::NAME)) {
            return $map;
        }

        $attributeName = $schemaAttributes->get(AttributeName::NAME);

        if (!is_string($attributeName)) {
            return $map;
        }

        return $map->add(
            $reflectionProperty->getName(),
            $attributeName
        );
    }

    /**
     * @throws \ReflectionException
     */
    public function denormalize(
        string $propertyName,
        string $class = null,
        string $format = null,
        array $context = []
    ): string {
        return $this
            ->getNameMap($class, $format)
            ->getDenormalized($propertyName);
    }
}
