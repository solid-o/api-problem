<?php

declare(strict_types=1);

namespace Solido\ApiProblem\Http;

use ReflectionClass;
use ReflectionProperty;
use Solido\ApiProblem\ApiProblemInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

use function is_object;
use function is_scalar;
use function is_string;
use function method_exists;
use function preg_replace;
use function sprintf;
use function strtolower;
use function trim;

class ApiProblem implements ApiProblemInterface
{
    public const TYPE_HTTP_RFC = 'http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html';
    private const TYPE_REF = 'https://solid-o.io/api-problem/%s.html';

    public int $status;
    public string $type;
    public string $title;
    public string $detail;
    public string|null $instance;

    /** @param array<string, mixed> $data */
    public function __construct(int $statusCode, private array $data = [])
    {
        $title = $data['title'] ?? Response::$statusTexts[$statusCode] ?? null;
        $this->status = $statusCode;
        $this->title = $title ?? 'Unknown';
        $this->type = $data['type'] ?? isset($data['title']) || ! isset($title) ? self::TYPE_HTTP_RFC : sprintf(
            self::TYPE_REF,
            strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title))),
        );

        $detail = $data['detail'] ?? '';
        if (is_scalar($detail) || (is_object($detail) && method_exists($detail, '__toString'))) {
            $this->detail = (string) $detail;
        } else {
            $this->detail = '';
        }

        $this->instance = isset($data['instance']) && is_string($data['instance']) ? $data['instance'] : null;
        unset($this->data['status'], $this->data['type'], $this->data['title'], $this->data['detail'], $this->data['instance']);
    }

    public function toResponse(): Response
    {
        return new JsonResponse($this->jsonSerialize(), $this->status, ['Content-Type' => 'application/problem+json']);
    }

    /** @return array<string, mixed> */
    public function jsonSerialize(): array
    {
        $reflection = new ReflectionClass($this);
        $properties = $reflection->getProperties(ReflectionProperty::IS_PUBLIC);

        $result = $this->data;
        foreach ($properties as $property) {
            $result[$property->getName()] = $property->getValue($this);
        }

        return $result;
    }
}
