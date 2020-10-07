<?php

namespace AcmeCorp\ReferenceExtension;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Form\Form;
use Symfony\Component\Mime\Address;
use Tightenco\Collect\Support\Collection;

class FormAction extends Collection
{
    /** @var Form */
    private $form;

    public function __construct(array $action, Form $form)
    {
        parent::__construct($action);
        $this->form = $form;
    }

    public function run(): void
    {
        $email = new TemplatedEmail();
    }

    private function getFrom(): Address
    {
        $email = $this->form->getConfig()->get
        return new Address($email, $name);
    }
}