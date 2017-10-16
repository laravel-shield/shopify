<?php

namespace Shield\Shopify\Test\Unit;

use PHPUnit\Framework\Assert;
use Shield\Shield\Contracts\Service;
use Shield\Shopify\Shopify;
use Shield\Testing\TestCase;

class ShopifyTest extends TestCase
{
    /**
     * @var Shopify
     */
    protected $service;

    protected function setUp()
    {
        parent::setUp();

        $this->service = new Shopify;
    }

    /** @test */
    public function it_is_a_service()
    {
        Assert::assertInstanceOf(Service::class, new Shopify);
    }

    /** @test */
    public function it_can_verify_a_valid_request()
    {
        $token = 'g00dk3$$';

        $this->app['config']['shield.services.shopify.token'] = $token;

        $content = 'The source of Truth';

        $request = $this->request($content);

        $headers = [
            'X-Shopify-Hmac-SHA256' => base64_encode(hash_hmac('sha256', $content, $token, true)),
        ];

        $request->headers->add($headers);

        Assert::assertTrue($this->service->verify($request, collect($this->app['config']['shield.services.shopify'])));
    }

    /** @test */
    public function it_will_not_verify_a_bad_request()
    {
        $content = 'The source of Truth';

        $request = $this->request($content);

        $headers = [
            'X-Shopify-Hmac-SHA256' => base64_encode(hash_hmac('sha256', $content, "oops", true)),
        ];

        $request->headers->add($headers);

        Assert::assertFalse($this->service->verify($request, collect($this->app['config']['shield.services.shopify'])));
    }

    /** @test */
    public function it_has_correct_headers_required()
    {
        Assert::assertArraySubset(['X-Shopify-Hmac-SHA256'], $this->service->headers());
    }
}
