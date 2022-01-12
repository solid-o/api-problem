<?php

declare(strict_types=1);

namespace Solido\ApiProblem\Tests;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Solido\ApiProblem\SerializableForm;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormConfigBuilder;
use Symfony\Component\Form\FormError;
use Symfony\Contracts\Translation\TranslatorInterface;

class SerializableFormTest extends TestCase
{
    use ProphecyTrait;

    public function testToArray(): void
    {
        $dispatcher = $this->prophesize(EventDispatcherInterface::class);

        $formConfigBuilder = new FormConfigBuilder('foo', null, $dispatcher->reveal());
        $formConfigBuilder->setCompound(true);
        $formConfigBuilder->setDataMapper($this->prophesize(DataMapperInterface::class)->reveal());
        $fooConfig = $formConfigBuilder->getFormConfig();

        $form = new Form($fooConfig);
        $form->addError(new FormError('This is the form error'));

        $formConfigBuilder = new FormConfigBuilder('bar', null, $dispatcher->reveal());
        $barConfig = $formConfigBuilder->getFormConfig();
        $child = new Form($barConfig);
        $child->addError(new FormError('Error of the child form'));
        $form->add($child);

        $translator = $this->prophesize(TranslatorInterface::class);
        $translator->trans(Argument::cetera())->willReturnArgument(0);

        $serializable = new SerializableForm($form, $translator->reveal());
        self::assertEquals('foo', $serializable->getName());

        $error = new FormError('This is the form error');
        $error->setOrigin($form);
        self::assertEquals([$error], $serializable->getErrors());
        self::assertEquals([new SerializableForm($form->get('bar'), $translator->reveal())], $serializable->getChildren());

        self::assertEquals([
            'name' => 'foo',
            'errors' => ['This is the form error'],
            'children' => [
                [
                    'name' => 'bar',
                    'errors' => ['Error of the child form'],
                    'children' => [],
                ],
            ],
        ], $serializable->toArray());
    }

    public function testShouldWorkWithoutATranslatorInstance(): void
    {
        $dispatcher = $this->prophesize(EventDispatcherInterface::class);

        $formConfigBuilder = new FormConfigBuilder('foo', null, $dispatcher->reveal());
        $formConfigBuilder->setCompound(true);
        $formConfigBuilder->setDataMapper($this->prophesize(DataMapperInterface::class)->reveal());
        $fooConfig = $formConfigBuilder->getFormConfig();

        $error = new FormError('This is the form error', 'This is the error template', ['{{ value }}' => 'invalid'], 2);

        $form = new Form($fooConfig);
        $form->addError($error);

        $translator = $this->prophesize(TranslatorInterface::class);
        $translator->trans('This is the error template', ['%count%' => 2, '{{ value }}' => 'invalid'], 'validators')->willReturn('Form error');

        $serializable = new SerializableForm($form, $translator->reveal());
        self::assertEquals([
            'name' => 'foo',
            'errors' => ['Form error'],
            'children' => [],
        ], $serializable->toArray());
    }

    public function testShouldWorkWithATranslatorInstance(): void
    {
        $dispatcher = $this->prophesize(EventDispatcherInterface::class);

        $formConfigBuilder = new FormConfigBuilder('foo', null, $dispatcher->reveal());
        $formConfigBuilder->setCompound(true);
        $formConfigBuilder->setDataMapper($this->prophesize(DataMapperInterface::class)->reveal());
        $fooConfig = $formConfigBuilder->getFormConfig();

        $form = new Form($fooConfig);
        $form->addError(new FormError('This is the form error'));

        $serializable = new SerializableForm($form);
        self::assertEquals([
            'name' => 'foo',
            'errors' => ['This is the form error'],
            'children' => [],
        ], $serializable->toArray());
    }
}
