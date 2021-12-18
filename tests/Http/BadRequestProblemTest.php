<?php

declare(strict_types=1);

namespace Solido\ApiProblem\Tests\Http;

use PHPUnit\Framework\TestCase;
use Solido\ApiProblem\Http\BadRequestProblem;

class BadRequestProblemTest extends TestCase
{
    public function testDefinesCorrectStatusCode(): void
    {
        $problem = new BadRequestProblem('bad request detail');

        self::assertEquals([
            'status' => 400,
            'type' => 'http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html',
            'title' => 'Bad Request',
            'detail' => 'bad request detail',
        ], $problem->jsonSerialize());
    }
}
