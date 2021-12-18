<?php

declare(strict_types=1);

namespace Solido\ApiProblem\Tests\Http;

use PHPUnit\Framework\TestCase;
use Solido\ApiProblem\Http\MethodNotAllowed;

class MethodNotAllowedTest extends TestCase
{
    public function testDefinesCorrectStatusCode(): void
    {
        $problem = new MethodNotAllowed('detail');

        self::assertEquals([
            'status' => 405,
            'type' => 'http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html',
            'title' => 'Method Not Allowed',
            'detail' => 'detail',
        ], $problem->jsonSerialize());
    }

    public function testDefinesGoodInvalidMethodMessage(): void
    {
        $problem = MethodNotAllowed::invalidMethod(['GET', 'POST', 'PUT'], 'PATCH');

        self::assertEquals([
            'status' => 405,
            'type' => 'http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html',
            'title' => 'Method Not Allowed',
            'detail' => 'PATCH not allowed. Should be: GET, POST or PUT',
        ], $problem->jsonSerialize());
    }
}
