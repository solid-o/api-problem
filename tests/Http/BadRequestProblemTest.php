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
            'type' => 'https://solid-o.io/api-problem/bad-request.html',
            'title' => 'Bad Request',
            'detail' => 'bad request detail',
            'instance' => null,
        ], $problem->jsonSerialize());
    }
}
