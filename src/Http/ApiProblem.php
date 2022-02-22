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
use function method_exists;

class ApiProblem implements ApiProblemInterface
{
    public const TYPE_HTTP_RFC = 'http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html';

    public int $status;
    public string $type = self::TYPE_HTTP_RFC;
    public string $title;
    public string $detail;

    /** @var array<string, mixed> */
    private array $data;

    /**
     * @param array<string, mixed> $data
     */
    public function __construct(int $statusCode, array $data = [])
    {
        $this->status = $statusCode;
        $this->title = $data['title'] ?? Response::$statusTexts[$statusCode] ?? 'Unknown';

        $detail = $data['detail'] ?? '';
        if (is_scalar($detail) || (is_object($detail) && method_exists($detail, '__toString'))) {
            $this->detail = (string) $detail;
        } else {
            $this->detail = '';
        }

        unset($data['status'], $data['type'], $data['title'], $data['detail']);
        $this->data = $data;
    }

    public function toResponse(): Response
    {
        return new JsonResponse($this->jsonSerialize(), $this->status, ['Content-Type' => 'application/problem+json']);
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        $reflection = new ReflectionClass($this);
        $properties = $reflection->getProperties(ReflectionProperty::IS_PUBLIC);

        $result = [];
        foreach ($this->data as $key => $value) {
            $result[$key] = $value;
        }

        foreach ($properties as $property) {
            $result[$property->getName()] = $property->getValue($this);
        }

        return $result;
    }
}
