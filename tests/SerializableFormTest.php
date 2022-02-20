<?php

declare(strict_types=1);

namespace Solido\ApiProblem\Tests;

use PHPUnit\Framework\TestCase;
use Solido\ApiProblem\SerializableMappingResult;
use Solido\DataMapper\MappingResultInterface;

class SerializableFormTest extends TestCase
{
    public function testToArray(): void
    {
        $barResult = new class implements MappingResultInterface {
            public function getName(): string
            {
                return 'bar';
            }

            public function getChildren(): array
            {
                return [];
            }

            public function getErrors(): array
            {
                return ['Error of the child form'];
            }
        };

        $mappingResult = new class ($barResult) implements MappingResultInterface {
            private MappingResultInterface $barResult;

            public function __construct(MappingResultInterface $barResult)
            {
                $this->barResult = $barResult;
            }

            public function getName(): string
            {
                return 'foo';
            }

            public function getChildren(): array
            {
                return [$this->barResult];
            }

            public function getErrors(): array
            {
                return ['This is the form error'];
            }
        };

        $serializable = new SerializableMappingResult($mappingResult);
        self::assertEquals('foo', $serializable->getName());

        self::assertEquals(['This is the form error'], $serializable->getErrors());
        self::assertEquals([new SerializableMappingResult($barResult)], $serializable->getChildren());

        self::assertEquals([
            'name' => 'foo',
            'errors' => ['This is the form error'],
            'children' => [
                [
                    'name' => 'bar',
                    'errors' => ['Error of the child form'],
                    'children' => [],
                ],
            ],
        ], $serializable->toArray());
    }
}
