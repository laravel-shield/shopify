<?php

namespace Shield\Shopify;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Shield\Shield\Contracts\Service;

class Shopify implements Service
{
    public function verify(Request $request, Collection $config): bool
    {

        $generated = base64_encode(hash_hmac('sha256', $request->getContent(), $config->get("token"), true));

        return hash_equals($generated, $request->header('X-Shopify-Hmac-SHA256'));
    }

    public function headers(): array
    {
        return ['X-Shopify-Hmac-SHA256'];
    }
}
