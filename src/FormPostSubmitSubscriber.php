<?php

namespace Bolt\BoltFormsExtraRecipients;

use AcmeCorp\ReferenceExtension\FormAction;
use Bolt\BoltForms\Event\PostSubmitEvent;
use Bolt\Extension\ExtensionRegistry;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Form;
use Tightenco\Collect\Support\Collection;

class FormPostSubmitSubscriber implements EventSubscriberInterface
{
    /** @var ExtensionRegistry */
    private $registry;

    public function __construct(ExtensionRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function run(PostSubmitEvent $event): void
    {
        $form = $event->getForm();

        if (! $form->isValid()) {
            return;
        }

        $actions = $this->getFormActions($form);

        array_map('self::run', $actions);
    }

    private function getFormActions(Form $form): array
    {
        $formName = $form->getName();
        $actions = collect([]);

        foreach($this->getConfig()->get('actions', []) as $action) {
            if (array_key_exists('form', $action) && $action['form'] === $formName) {
                $actions->add(new FormAction($action, $form));
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