<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Objects\SchemaResolvers;

use Assert\Assert;
use FlixTech\AvroSerializer\Objects\SchemaResolverInterface;

class FileResolver implements SchemaResolverInterface
{
    private string $baseDir;

    /**
     * @var callable
     */
    private $inflector;

    public function __construct(string $baseDir, callable $inflector)
    {
        $this->baseDir = $baseDir;
        $this->inflector = $inflector;
    }

    /**
     * @throws \AvroSchemaParseException
     */
    public function valueSchemaFor(mixed $record): \AvroSchema
    {
        $inflectedFileName = \call_user_func($this->inflector, $record, false);
        Assert::that($inflectedFileName)->string()->notEmpty();

        $filePath = $this->getFilePath($inflectedFileName);

        Assert::that($filePath)->string()->file();

        return \AvroSchema::parse((string) @\file_get_contents($filePath));
    }

    /**
     * @throws \AvroSchemaParseException
     */
    public function keySchemaFor(mixed $record): ?\AvroSchema
    {
        $inflectedFileName = \call_user_func($this->inflector, $record, true);
        Assert::that($inflectedFileName)->string()->notEmpty();

        $fileContents = @\file_get_contents($this->getFilePath($inflectedFileName));

        if (false === $fileContents) {
            return null;
        }

        return \AvroSchema::parse($fileContents);
    }

    private function getFilePath(string $inflectedFileName): string
    {
        return \sprintf('%s/%s',
            $this->baseDir,
            $inflectedFileName
        );
    }
}
