<?php

namespace Drupal\contact_mail\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;


class ContactMailForm extends ContentEntityForm
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $form = parent::buildForm($form, $form_state);
        return $form;
    }

    public function validateForm(array &$form, FormStateInterface $form_state)
    {
        parent::validateForm($form, $form_state);
    }

    /**
     * {@inheritdoc}
     */
    public function save(array $form, FormStateInterface $form_state)
    {
        $entity = $this->entity;
        $status = parent::save($form, $form_state);
        $entity_label = $this->entity->label();

        switch ($status) {
            case SAVED_NEW:
                $this->messenger()->addStatus("{$entity_label}を作成しました。");
                break;

            default:
                $this->messenger()->addStatus("{$entity_label}を更新しました。");
        }

        $form_state->setRedirect('entity.contact_mail.canonical', ['contact_mail' => $entity->id()]);
    }
}
