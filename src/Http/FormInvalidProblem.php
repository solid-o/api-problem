<?php

declare(strict_types=1);

namespace Solido\ApiProblem\Http;

use Solido\ApiProblem\SerializableForm;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

class FormInvalidProblem extends ApiProblem
{
    public const TYPE_FORM_INVALID = 'https://solid-o.io/api-problem/invalid-form.html';

    /** @var array<string, mixed> */
    public array $errors = [];
    public string $type = self::TYPE_FORM_INVALID;

    public function __construct(FormInterface $form, ?TranslatorInterface $translator = null)
    {
        parent::__construct(Response::HTTP_BAD_REQUEST, ['detail' => 'form is invalid']);

        $serializableForm = new SerializableForm($form, $translator);
        $this->errors = $serializableForm->toArray();
    }
}
