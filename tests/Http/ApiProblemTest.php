<?php

declare(strict_types=1);

namespace Solido\ApiProblem\Tests\Http;

use PHPUnit\Framework\TestCase;
use Solido\ApiProblem\Http\ApiProblem;

use function fopen;

class ApiProblemTest extends TestCase
{
    public function testDefaultTitleAndDetails(): void
    {
        $problem = new ApiProblem(500, ['instance' => '/test']);
        self::assertEquals([
            'status' => 500,
            'type' => 'https://solid-o.io/api-problem/internal-server-error.html',
            'title' => 'Internal Server Error',
            'detail' => '',
            'instance' => '/test',
        ], $problem->jsonSerialize());
    }

    public function testCustomType(): void
    {
        $problem = new ApiProblem(408, ['type' => 'https://solid-o.test/api-problem/test.html']);
        self::assertEquals([
            'status' => 408,
            'type' => 'https://solid-o.test/api-problem/test.html',
            'title' => 'Request Timeout',
            'detail' => '',
            'instance' => null,
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
            'instance' => null,
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
            'instance' => null,
        ], $problem->jsonSerialize());
    }

    /**
     * @dataProvider provideDetails
     */
    public function testDefinedDetails($expected, $detail): void
    {
        $problem = new ApiProblem(100, ['detail' => $detail]);

        self::assertEquals([
            'status' => 100,
            'type' => 'https://solid-o.io/api-problem/continue.html',
            'title' => 'Continue',
            'detail' => $expected,
            'instance' => null,
        ], $problem->jsonSerialize());
    }

    public function provideDetails(): iterable
    {
        yield ['', null];
        yield ['Foo example details', 'Foo example details'];
        yield ['42', 42];
        yield [
            'Example from object',
            new class {
                public function __toString(): string
                {
                    return 'Example from object';
                }
            },
        ];

        yield ['', fopen('php://temp', 'rb')];
    }

    public function testDefinedDetailsAndExtraFields(): void
    {
        $problem = new ApiProblem(301, [
            'detail' => 'Foo example details',
            'extra_field' => 'This is extra',
        ]);

        self::assertEquals([
            'status' => 301,
            'type' => 'https://solid-o.io/api-problem/moved-permanently.html',
            'title' => 'Moved Permanently',
            'detail' => 'Foo example details',
            'extra_field' => 'This is extra',
            'instance' => null,
        ], $problem->jsonSerialize());
    }
}
