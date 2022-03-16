<?php

namespace App\Form\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class RemoveFieldsOnEdit implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::PRE_SET_DATA => 'onPreSetData'
        ];
    }

    public function onPreSetData(FormEvent $event): void
    {
        $trick = $event->getData();
        $form = $event->getForm();

        if (null !== $trick->getId()) {

            $form->remove('mainPicture');
            $form->remove('pictures');
            $form->remove('videos');
        }
    }
}
