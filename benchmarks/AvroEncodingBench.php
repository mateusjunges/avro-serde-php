<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Benchmarks;

use AvroIOException;
use AvroSchema;
use AvroSchemaParseException;
use Exception;
use FlixTech\AvroSerializer\Objects\RecordSerializer;
use FlixTech\SchemaRegistryApi\Exception\SchemaRegistryException;
use FlixTech\SchemaRegistryApi\Registry;
use FlixTech\SchemaRegistryApi\Registry\BlockingRegistry;
use FlixTech\SchemaRegistryApi\Registry\Cache\AvroObjectCacheAdapter;
use FlixTech\SchemaRegistryApi\Registry\CachedRegistry;
use FlixTech\SchemaRegistryApi\Registry\PromisingRegistry;
use GuzzleHttp\Client;
use GuzzleHttp\Promise\PromiseInterface;
use PhpBench\Benchmark\Metadata\Annotations\BeforeMethods;
use PhpBench\Benchmark\Metadata\Annotations\Iterations;
use PhpBench\Benchmark\Metadata\Annotations\Revs;

#[\PhpBench\Attributes\BeforeMethods('setUp')]
class AvroEncodingBench
{
    public const ASYNC = 'async';
    public const ASYNC_CACHED = 'async_cached';
    public const SYNC = 'sync';
    public const SYNC_CACHED = 'sync_cached';

    public const TEST_MODES = [
        self::ASYNC,
        self::ASYNC_CACHED,
        self::SYNC,
        self::SYNC_CACHED,
    ];

    public const TEST_RECORD = [
        'name' => 'Thomas',
        'age' => 36,
    ];

    public const SCHEMA_JSON = /** @lang JSON */
        <<<JSON
{
  "type": "record",
  "name": "user",
  "fields": [
    {"name": "name", "type": "string"},
    {"name": "age", "type": "int"}
  ]
}
JSON;

    /**
     * @var RecordSerializer[]
     */
    private array $serializers = [];

    /**
     * @var string[]
     */
    private array $messages = [];

    /**
     * @var AvroSchema
     */
    private AvroSchema $schema;

    /**
     * @throws AvroSchemaParseException
     * @throws SchemaRegistryException
     * @throws AvroIOException
     */
    public function setUp(): void
    {
        $this->schema = AvroSchema::parse(self::SCHEMA_JSON);

        $this->prepareTestForMode(self::ASYNC, new PromisingRegistry(
            new Client(['base_uri' => getenv('SCHEMA_REGISTRY_HOST')])
        ));

        $this->prepareTestForMode(self::SYNC, new BlockingRegistry(
            new PromisingRegistry(
                new Client(['base_uri' => getenv('SCHEMA_REGISTRY_HOST')])
            )
        ));

        $this->prepareTestForMode(self::ASYNC_CACHED, new CachedRegistry(
            new PromisingRegistry(
                new Client(['base_uri' => getenv('SCHEMA_REGISTRY_HOST')])
            ),
            new AvroObjectCacheAdapter()
        ));

        $this->prepareTestForMode(self::SYNC_CACHED, new CachedRegistry(
            new BlockingRegistry(
                new PromisingRegistry(
                    new Client(['base_uri' => getenv('SCHEMA_REGISTRY_HOST')])
                )
            ),
            new AvroObjectCacheAdapter()
        ));
    }

    /**
     * @throws Exception
     * @throws SchemaRegistryException
     */
    #[\PhpBench\Attributes\Revs(1000)]
    #[\PhpBench\Attributes\Iterations(5)]
    public function benchEncodeWithSyncRegistry(): void
    {
        $this->serializers[self::SYNC]->encodeRecord('test', $this->schema, self::TEST_RECORD);
    }

    /**
     * @throws Exception
     * @throws SchemaRegistryException
     */
    #[\PhpBench\Attributes\Revs(1000)]
    #[\PhpBench\Attributes\Iterations(5)]
    public function benchDecodeWithSyncRegistry(): void
    {
        $this->serializers[self::SYNC]->decodeMessage($this->messages[self::SYNC]);
    }

    /**
     * @throws Exception
     * @throws SchemaRegistryException
     */
    #[\PhpBench\Attributes\Revs(1000)]
    #[\PhpBench\Attributes\Iterations(5)]
    public function benchEncodeWithAsyncRegistry(): void
    {
        $this->serializers[self::ASYNC]->encodeRecord('test', $this->schema, self::TEST_RECORD);
    }

    /**
     * @throws Exception
     * @throws SchemaRegistryException
     */
    #[\PhpBench\Attributes\Revs(1000)]
    #[\PhpBench\Attributes\Iterations(5)]
    public function benchDecodeWithAsyncRegistry(): void
    {
        $this->serializers[self::ASYNC]->decodeMessage($this->messages[self::ASYNC]);
    }

    /**
     * @throws Exception
     * @throws SchemaRegistryException
     */
    #[\PhpBench\Attributes\Revs(1000)]
    #[\PhpBench\Attributes\Iterations(5)]
    public function benchEncodeWithAsyncCachedRegistry(): void
    {
        $this->serializers[self::ASYNC_CACHED]->encodeRecord('test', $this->schema, self::TEST_RECORD);
    }

    /**
     * @throws Exception
     * @throws SchemaRegistryException
     */
    #[\PhpBench\Attributes\Revs(1000)]
    #[\PhpBench\Attributes\Iterations(5)]
    public function benchDecodeWithAsyncCachedRegistry(): void
    {
        $this->serializers[self::ASYNC_CACHED]->decodeMessage($this->messages[self::ASYNC_CACHED]);
    }


    /**
     * @throws Exception
     * @throws SchemaRegistryException
     */
    #[\PhpBench\Attributes\Revs(1000)]
    #[\PhpBench\Attributes\Iterations(5)]
    public function benchEncodeWithSyncCachedRegistry(): void
    {
        $this->serializers[self::SYNC_CACHED]->encodeRecord('test', $this->schema, self::TEST_RECORD);
    }

    /**
     * @throws Exception
     * @throws SchemaRegistryException
     */
    #[\PhpBench\Attributes\Revs(1000)]
    #[\PhpBench\Attributes\Iterations(5)]
    public function benchDecodeWithSyncCachedRegistry(): void
    {
        $this->serializers[self::SYNC_CACHED]->decodeMessage($this->messages[self::SYNC_CACHED]);
    }

    /**
     * @param string                               $mode
     * @param Registry $registry
     *
     * @throws SchemaRegistryException|AvroIOException
     */
    private function prepareTestForMode(string $mode, Registry $registry): void
    {
        $result = $registry->register('test', $this->schema);
        !$result instanceof PromiseInterface ?: $result->wait();

        $this->serializers[$mode] = new RecordSerializer($registry);

        try {
            $this->messages[$mode] = $this->serializers[$mode]->encodeRecord(
                'test',
                $this->schema,
                self::TEST_RECORD
            );
        } catch (Exception|SchemaRegistryException $e) {
        }
    }
}
