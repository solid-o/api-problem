<?php

declare(strict_types=1);

namespace Solido\ApiProblem\Tests\Http;

use PHPUnit\Framework\TestCase;
use Solido\ApiProblem\Http\ApiProblem;

class ApiProblemTest extends TestCase
{
    public function testDefaultTitleAndDetails(): void
    {
        $problem = new ApiProblem(500);
        self::assertEquals([
            'status' => 500,
            'type' => 'http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html',
            'title' => 'Internal Server Error',
            'detail' => '',
        ], $problem->jsonSerialize());
    }

    public function testDefaultTitleAndDetailsWithNonStandardStatusCode(): void
    {
        $problem = new ApiProblem(599);
        self::assertEquals([
            'status' => 599,
            'type' => 'http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html',
            'title' => 'Unknown',
            'detail' => '',
        ], $problem->jsonSerialize());
    }

    public function testDefinedTitle(): void
    {
        $problem = new ApiProblem(200, ['title' => 'All good']);

        self::assertEquals([
            'status' => 200,
            'type' => 'http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html',
            'title' => 'All good',
            'detail' => '',
        ], $problem->jsonSerialize());
    }

    public function testDefinedDetails(): void
    {
        $problem = new ApiProblem(100, ['detail' => 'Foo example details']);

        self::assertEquals([
            'status' => 100,
            'type' => 'http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html',
            'title' => 'Continue',
            'detail' => 'Foo example details',
        ], $problem->jsonSerialize());
    }

    public function testDefinedDetailsAndExtraFields(): void
    {
        $problem = new ApiProblem(301, [
            'detail' => 'Foo example details',
            'extra_field' => 'This is extra',
        ]);

        self::assertEquals([
            'status' => 301,
            'type' => 'http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html',
            'title' => 'Moved Permanently',
            'detail' => 'Foo example details',
            'extra_field' => 'This is extra',
        ], $problem->jsonSerialize());
    }
}
