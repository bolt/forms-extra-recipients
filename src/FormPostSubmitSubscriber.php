<?php

declare(strict_types=1);

namespace Bolt\BoltFormsExtraRecipients;

use Bolt\BoltForms\Event\PostSubmitEvent;
use Bolt\Extension\ExtensionRegistry;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Mailer\MailerInterface;
use Tightenco\Collect\Support\Collection;

class FormPostSubmitSubscriber implements EventSubscriberInterface
{
    /** @var ExtensionRegistry */
    private $registry;

    /** @var MailerInterface */
    private $mailer;

    public function __construct(ExtensionRegistry $registry, MailerInterface $mailer)
    {
        $this->registry = $registry;
        $this->mailer = $mailer;
    }

    public function run(PostSubmitEvent $event): void
    {
        $form = $event->getForm();

        if (! $form->isValid()) {
            return;
        }

        $actions = $this->getFormActions($event, $form);

        /** @var FormAction $action */
        foreach ($actions as $action) {
            $this->mailer->send($action->buildEmail());
        }
    }

    private function getFormActions(PostSubmitEvent $event, Form $form): array
    {
        $formName = $form->getName();
        $actions = collect([]);

        $config = $event->getFormConfig();
        $generalFormsConfig = $event->getConfig();

        // There should be a better way to handle this.
        if (! $config->has('templates')) {
            $config->put('templates', $event->getConfig()->get('templates'));
        }

        foreach ($this->getConfig()->get('actions', []) as $name => $action) {
            if (array_key_exists('form', $action) && $action['form'] === $formName) {
                $actions->add(new FormAction($name, $action, $form, $config, $generalFormsConfig, $event->getMeta()));
            }
        }

        return $actions->toArray();
    }

    private function getConfig(): Collection
    {
        $extension = $this->registry->getExtension('Bolt\\BoltFormsExtraRecipients');

        return $extension->getConfig();
    }

    public static function getSubscribedEvents()
    {
        return [
            'boltforms.post_submit' => ['run', 50],
        ];
    }
}
