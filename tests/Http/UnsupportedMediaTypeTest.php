<?php

declare(strict_types=1);

namespace Solido\ApiProblem\Tests\Http;

use PHPUnit\Framework\TestCase;
use Solido\ApiProblem\Http\UnsupportedMediaType;

class UnsupportedMediaTypeTest extends TestCase
{
    public function testDefinesCorrectStatusCode(): void
    {
        $problem = new UnsupportedMediaType('detail');

        self::assertEquals([
            'status' => 415,
            'type' => 'http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html',
            'title' => 'Unsupported Media Type',
            'detail' => 'detail',
        ], $problem->jsonSerialize());
    }

    public function testDefinesGoodInvalidContentTypeMessage(): void
    {
        $problem = UnsupportedMediaType::invalidContentType(['application/json', 'application/xml', 'application/x-www-form-urlencoded'], 'form/multipart-data');

        self::assertEquals([
            'status' => 415,
            'type' => 'http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html',
            'title' => 'Unsupported Media Type',
            'detail' => '"form/multipart-data" is not supported. Supported content types are: "application/json", "application/xml" and "application/x-www-form-urlencoded"',
        ], $problem->jsonSerialize());
    }

    public function testDefinesGoodInvalidEncodingMessage(): void
    {
        $problem = UnsupportedMediaType::invalidContentEncoding(['application/json', 'application/xml'], ['text/plain', 'text/html', 'application/yaml']);

        self::assertEquals([
            'status' => 415,
            'type' => 'http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html',
            'title' => 'Unsupported Media Type',
            'detail' => 'None of the passed encodings ("text/plain", "text/html" and "application/yaml") are allowed. Should contain at least one of: "application/json", "application/xml"',
        ], $problem->jsonSerialize());
    }
}
