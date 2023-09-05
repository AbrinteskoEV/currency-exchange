<?php

declare(strict_types=1);

namespace Tests\Infrastructure\Service\Cache;

use Infrastructure\Service\Cache\TarantoolCacheRepository;
use PHPUnit\Framework\TestCase;

class TarantoolCacheRepositoryTest extends TestCase
{
    private const TEST_CACHE_KEY = 'test';

    private TarantoolCacheRepository $tarantoolCacheRepository;

    /**
     * @return void
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->tarantoolCacheRepository = app()->make(TarantoolCacheRepository::class);
        $this->tarantoolCacheRepository->remove(self::TEST_CACHE_KEY);
    }

    /**
     * @return void
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        $this->tarantoolCacheRepository->remove(self::TEST_CACHE_KEY);
    }

    /**
     * @return void
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     */
    public function testUnspecifiedDataGetting(): void
    {
        $unspecifiedData = $this->tarantoolCacheRepository->retrieve(self::TEST_CACHE_KEY);
        self::assertEquals(null, $unspecifiedData, 'Unspecified data getting test');
    }

    /**
     * @return void
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     */
    public function testSpecifiedDataGetting(): void
    {
        $this->tarantoolCacheRepository->store(self::TEST_CACHE_KEY, 'test');
        $specifiedData = $this->tarantoolCacheRepository->retrieve(self::TEST_CACHE_KEY);
        self::assertEquals('test', $specifiedData,'Specified data getting test');
    }

    /**
     * @return void
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     */
    public function testDataOverwriting(): void
    {
        $this->tarantoolCacheRepository->store(self::TEST_CACHE_KEY, 'test1');
        $this->tarantoolCacheRepository->store(self::TEST_CACHE_KEY, 'test2');
        $overwrittenData = $this->tarantoolCacheRepository->retrieve(self::TEST_CACHE_KEY);
        self::assertEquals('test2', $overwrittenData, 'Data overwriting test');
    }

    /**
     * @return void
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     */
    public function testDataDeletion(): void
    {
        $this->tarantoolCacheRepository->store(self::TEST_CACHE_KEY, 'test');
        $this->tarantoolCacheRepository->remove(self::TEST_CACHE_KEY);
        $deletedData = $this->tarantoolCacheRepository->retrieve(self::TEST_CACHE_KEY);
        self::assertEquals(null, $deletedData, 'Data deletion test');
    }
}
