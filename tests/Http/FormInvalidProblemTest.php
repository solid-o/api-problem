<?php

declare(strict_types=1);

namespace Solido\ApiProblem\Tests\Http;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Solido\ApiProblem\Http\FormInvalidProblem;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormConfigBuilder;
use Symfony\Component\Form\FormError;

class FormInvalidProblemTest extends TestCase
{
    use ProphecyTrait;

    public function testDefinesCorrectStatusCode(): void
    {
        $dispatcher = $this->prophesize(EventDispatcherInterface::class);

        $formConfigBuilder = new FormConfigBuilder('foo', null, $dispatcher->reveal());
        $formConfigBuilder->setCompound(true);
        $formConfigBuilder->setDataMapper($this->prophesize(DataMapperInterface::class)->reveal());
        $fooConfig = $formConfigBuilder->getFormConfig();

        $form = new Form($fooConfig);
        $form->addError(new FormError('This is the form error'));

        $problem = new FormInvalidProblem($form);

        self::assertEquals([
            'status' => 400,
            'type' => 'https://solid-o.io/api-problem/invalid-form.html',
            'title' => 'Bad Request',
            'detail' => 'form is invalid',
            'errors' => [
                'name' => 'foo',
                'errors' => ['This is the form error'],
                'children' => [],
            ],
        ], $problem->jsonSerialize());
    }
}
