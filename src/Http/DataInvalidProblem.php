<?php

declare(strict_types=1);

namespace Solido\ApiProblem\Http;

use Solido\ApiProblem\SerializableMappingResult;
use Solido\DataMapper\MappingResultInterface;
use Symfony\Component\HttpFoundation\Response;

class DataInvalidProblem extends ApiProblem
{
    public const TYPE_FORM_INVALID = 'https://solid-o.io/api-problem/invalid-data.html';

    /** @var array<string, mixed> */
    public array $errors = [];

    public function __construct(MappingResultInterface $mappingResult)
    {
        parent::__construct(Response::HTTP_BAD_REQUEST, ['detail' => 'form is invalid']);

        $serializableForm = new SerializableMappingResult($mappingResult);
        $this->errors = $serializableForm->toArray();
        $this->type = self::TYPE_FORM_INVALID;
    }
}
