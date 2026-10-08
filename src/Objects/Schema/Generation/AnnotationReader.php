<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Objects\Schema\Generation;

use Doctrine\Common\Annotations\Reader as DoctrineAnnotationReader;

class AnnotationReader implements SchemaAttributeReader
{
    private DoctrineAnnotationReader $reader;

    public function __construct(DoctrineAnnotationReader $reader)
    {
        $this->reader = $reader;
    }

    public function readClassAttributes(\ReflectionClass $class): SchemaAttributes
    {
        /** @var SchemaAttribute[] $annotations */
        $annotations = $this->reader->getClassAnnotations($class);
        $attributes = \array_filter($annotations, [$this, 'onlySchemaAttributes']);

        return new SchemaAttributes(...$attributes);
    }

    public function readPropertyAttributes(\ReflectionProperty $property): SchemaAttributes
    {
        /** @var SchemaAttribute[] $annotations */
        $annotations = $this->reader->getPropertyAnnotations($property);
        $attributes = \array_filter($annotations, [$this, 'onlySchemaAttributes']);

        return new SchemaAttributes(...$attributes);
    }

    private function onlySchemaAttributes(object $annotation): bool
    {
        return $annotation instanceof SchemaAttribute;
    }
}
