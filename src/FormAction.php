<?php

declare(strict_types=1);

namespace Bolt\BoltFormsExtraRecipients;

use Bolt\BoltForms\Factory\EmailFactory;
use Exception;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Form\Form;
use Tightenco\Collect\Support\Collection;

class FormAction
{
    /** @var Form */
    private $form;

    /** @var Collection */
    private $action;

    /** @var string */
    private $name;

    /** @var Collection */
    private $formConfig;

    /** @var array */
    private $meta = [];

    /** @var Collection */
    private $generalFormsConfig;

    public function __construct(string $name, array $action, Form $form, Collection $formConfig, Collection $generalFormsConfig, array $meta = [])
    {
        $this->form = $form;
        $this->action = collect($action);
        $this->name = $name;
        $this->formConfig = $formConfig;
        $this->meta = $meta;
        $this->generalFormsConfig = $generalFormsConfig;
    }

    public function buildEmail(): TemplatedEmail
    {
        $email = (new EmailFactory())->create($this->formConfig, $this->generalFormsConfig, $this->form, $this->meta);

        // Add recipients
        foreach ($this->getTo() as $recipient) {
            $email->addTo($recipient);
        }

        return $email;
    }

    private function getTo(): array
    {
        return array_merge($this->getToEmail(), $this->getToField());
    }

    private function getToEmail(): array
    {
        $to = collect($this->action->get('to'));
        $email = $to->get('email', []);

        if (! is_iterable($email)) {
            $email = [$email];
        }

        return $email;
    }

    private function getToField(): array
    {
        $to = collect($this->action->get('to'));
        $field = collect($to->get('field', false));

        $fieldName = $field->get('name', false);

        if (! $fieldName) {
            throw new Exception(sprintf("The <code>field</code> setting of the FormsExtraRecipients extension for action '%s' needs a name attribute.", $this->name));
        }

        if (! $this->form->get($fieldName)) {
            throw new Exception(sprintf("The field '%s' is expected, but it was not submitted with the '%s' form.", $fieldName, $this->form->getName()));
        }

        $data = $this->form->getData();

        if (! array_key_exists($fieldName, $data)) {
            return [];
        }

        $value = $data[$fieldName];
        $values = $field->get('values', []);

        if (! array_key_exists($value, $values)) {
            return [];
        }

        $emails = $values[$value];

        if (! is_array($emails)) {
            $emails = [$emails];
        }

        return $emails;
    }
}
