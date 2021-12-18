<?php

declare(strict_types=1);

namespace Solido\ApiProblem\Http;

use Symfony\Component\HttpFoundation\Response;

class BadRequestProblem extends ApiProblem
{
    public function __construct(string $detail)
    {
        parent::__construct(Response::HTTP_BAD_REQUEST, ['detail' => $detail]);
    }
}
