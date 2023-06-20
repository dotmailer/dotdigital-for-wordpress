<?php

namespace Dotdigital\Tests;

use Dotdigital\AbstractClient;
use PHPUnit\Framework\Assert;

trait ApiConfigurationTrait
{
    /**
     * @var AbstractClient
     */
    protected AbstractClient $client;

    protected function clientInit(): void
    {
        $this->client::setApiUser('demo@apiconnector.com');
        $this->client::setApiPassword('demo');
        $this->client::setApiEndpoint('https://r1-api.dotmailer.com');
    }

    /** @test */
    public function testSuccessResponse()
    {
        $response = $this->client->getHttpClient()->get($this->resourceBase);
        Assert::assertEquals(200, $response->getStatusCode());

        $contentType = $response->getHeaders()["Content-Type"][0];
        Assert::assertEquals("application/json; charset=utf-8", $contentType);
    }

    /** @test */
    public function testFailedResponse()
    {
        $this->client::setApiUser('invalid_ec_user');
        $this->client::setApiPassword('invalid_ec_password');
        $response = $this->client->getHttpClient()->get($this->resourceBase);;
        Assert::assertEquals(401, $response->getStatusCode());

        $contentType = $response->getHeaders()["Content-Type"][0];
        Assert::assertEquals("application/json; charset=utf-8", $contentType);
    }
}
