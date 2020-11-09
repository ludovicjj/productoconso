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
     * @inheritDoc
     */
    public function handle(Request $request, object $entity, $data = null, array $options = []): bool
    {
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
            $this->process($this->form, $entity);
            return true;
        }
        return false;
    }

    protected function configure(OptionsResolver $resolver): void
    {
    }

    abstract public function getFormFactory(): FormFactoryInterface;
    abstract public function process(FormInterface $form, object $entity): void;

    public function createView(): FormView
    {
        return $this->form->createView();
    }
}
