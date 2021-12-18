<?php

declare(strict_types=1);

namespace Solido\ApiProblem;

use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

use function array_map;
use function assert;

/**
 * @internal
 */
final class SerializableForm
{
    /** @var FormError[] */
    private array $errors = [];
    /** @var self[] */
    private array $children = [];
    private string $name;
    private ?TranslatorInterface $translator;

    public function __construct(FormInterface $form, ?TranslatorInterface $translator = null)
    {
        $this->translator = $translator;
        $this->name = $form->getName();

        foreach ($form->getErrors(false) as $error) {
            assert($error instanceof FormError);
            $this->errors[] = $error;
        }

        foreach ($form->all() as $child) {
            $this->children[] = new self($child, $translator);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'errors' => array_map([$this, 'getErrorMessage'], $this->errors),
            'children' => array_map(static fn (self $child) => $child->toArray(), $this->children),
        ];
    }

    /**
     * @return FormError[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @return self[]
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    public function getName(): string
    {
        return $this->name;
    }

    private function getErrorMessage(FormError $error): string
    {
        if ($this->translator === null) {
            return $error->getMessage();
        }

        if ($error->getMessagePluralization() !== null) {
            return $this->translator->trans(
                $error->getMessageTemplate(),
                ['%count%' => $error->getMessagePluralization()] + $error->getMessageParameters(),
                'validators'
            );
        }

        return $this->translator->trans($error->getMessageTemplate(), $error->getMessageParameters(), 'validators');
    }
}
