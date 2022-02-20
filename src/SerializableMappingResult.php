<?php

declare(strict_types=1);

namespace Solido\ApiProblem;

use Solido\DataMapper\MappingResultInterface;

use function array_map;

/**
 * @internal
 */
final class SerializableMappingResult implements MappingResultInterface
{
    private MappingResultInterface $decorated;

    public function __construct(MappingResultInterface $result)
    {
        $this->decorated = $result;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->decorated->getName(),
            'errors' => $this->decorated->getErrors(),
            'children' => array_map(static fn (self $child) => $child->toArray(), $this->getChildren()),
        ];
    }

    /**
     * @return string[]
     */
    public function getErrors(): array
    {
        return $this->decorated->getErrors();
    }

    /**
     * @return self[]
     */
    public function getChildren(): array
    {
        return array_map(static fn (MappingResultInterface $result) => new self($result), $this->decorated->getChildren());
    }

    public function getName(): string
    {
        return $this->decorated->getName();
    }
}
