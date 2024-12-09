<?php

declare(strict_types=1);

namespace Solido\ApiProblem\Tests\Http;

use PHPUnit\Framework\TestCase;
use Solido\ApiProblem\Http\DataInvalidProblem;
use Solido\DataMapper\MappingResultInterface;

class DataInvalidProblemTest extends TestCase
{
    public function testDefinesCorrectStatusCode(): void
    {
        $result = new class implements MappingResultInterface {
            public function getName(): string
            {
                return 'foo';
            }

            public function getChildren(): array
            {
                return [];
            }

            public function getErrors(): array
            {
                return ['This is the form error'];
            }
        };

        $problem = new DataInvalidProblem($result);

        self::assertEquals([
            'status' => 400,
            'type' => 'https://solid-o.io/api-problem/invalid-data.html',
            'title' => 'Bad Request',
            'detail' => 'form is invalid',
            'errors' => [
                'name' => 'foo',
                'errors' => ['This is the form error'],
                'children' => [],
            ],
            'instance' => null,
        ], $problem->jsonSerialize());
    }
}
