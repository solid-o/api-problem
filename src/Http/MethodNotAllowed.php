<?php

declare(strict_types=1);

namespace Solido\ApiProblem\Http;

use Symfony\Component\HttpFoundation\Response;

use function array_splice;
use function implode;
use function Safe\sprintf;

class MethodNotAllowed extends ApiProblem
{
    public function __construct(string $detail)
    {
        parent::__construct(Response::HTTP_METHOD_NOT_ALLOWED, ['detail' => $detail]);
    }

    /** @param string[] $allowMethods */
    public static function invalidMethod(array $allowMethods, string $currentMethod): self
    {
        $allowMethods[] = implode(' or ', array_splice($allowMethods, -2));

        return new self(sprintf('%s not allowed. Should be: %s', $currentMethod, implode(', ', $allowMethods)));
    }
}
