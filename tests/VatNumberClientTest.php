<?php

declare(strict_types=1);

namespace Tests\VatNumber;

use PHPUnit\Framework\TestCase;
use Vilkas\VatNumber\Client\VatNumberClient;

class VatNumberClientTest extends TestCase
{
    /**
     * @var VatNumberClient
     */
    protected $client;

    /**
     * Skip everything if environment variables are not available and the client cannot be setup.
     */
    protected function setUp(): void
    {
        $this->client = new VatNumberClient();
    }

    public function testGetVatInfoSuccess(): void
    {
        $results = $this->client->getVatInfo('FI', '10339964');
        $this->assertEquals(true, $results['isValid']);
    }
    public function testGetVatInfoFail(): void
    {
        $results = $this->client->getVatInfo('FI', '1033996');
        $this->assertEquals(false, $results['isValid']);
    }
}
