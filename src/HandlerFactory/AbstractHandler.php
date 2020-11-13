<?php

namespace App\HandlerFactory;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractHandler implements HandlerInterface
{
    /**
     * @var FormInterface $form
     */
    private $form;

    /**
     * @var null|object $entity
     */
    private $entity;

    /**
     * @inheritDoc
     */
    public function handle(Request $request, ?object $entity = null, $data = null, array $options = []): bool
    {
        $this->entity = $entity;

        $resolver = new OptionsResolver();
        $resolver->setRequired("form_type");
        $resolver->setRequired("form_options");
        $this->configure($resolver);

        $options = $resolver->resolve($options);

        $this->form = $this->getFormFactory()->create(
            $options['form_type'],
            $data,
            $options['form_options']
        )->handleRequest($request);


        if ($this->form->isSubmitted() && $this->form->isValid()) {
            $this->process($this->form);
            return true;
        }
        return false;
    }

    protected function configure(OptionsResolver $resolver): void
    {
    }

    abstract public function getFormFactory(): FormFactoryInterface;
    abstract public function process(FormInterface $form): void;

    /**
     * @return FormView
     */
    public function createView(): FormView
    {
        return $this->form->createView();
    }

    /**
     * @return object|null
     */
    protected function getEntity(): ?object
    {
        return $this->entity;
    }
}
