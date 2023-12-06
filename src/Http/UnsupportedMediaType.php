<?php

declare(strict_types=1);

namespace Solido\ApiProblem\Http;

use Symfony\Component\HttpFoundation\Response;

use function array_splice;
use function implode;
use function Safe\sprintf;

class UnsupportedMediaType extends ApiProblem
{
    public function __construct(string $detail)
    {
        parent::__construct(Response::HTTP_UNSUPPORTED_MEDIA_TYPE, ['detail' => $detail]);
    }

    /**
     * @param string[] $allowedEncodings
     * @param string[] $providedEncodings
     */
    public static function invalidContentEncoding(array $allowedEncodings, array $providedEncodings): self
    {
        $providedEncodings[] = implode('" and "', array_splice($providedEncodings, -2));

        return new self(sprintf('None of the passed encodings ("%s") are allowed. Should contain at least one of: "%s"', implode('", "', $providedEncodings), implode('", "', $allowedEncodings)));
    }

    /** @param string[] $allowedTypes */
    public static function invalidContentType(array $allowedTypes, string $providedType): self
    {
        $allowedTypes[] = implode('" and "', array_splice($allowedTypes, -2));

        return new self(sprintf('"%s" is not supported. Supported content types are: "%s"', $providedType, implode('", "', $allowedTypes)));
    }
}
