<?php

namespace Drupal\contact_mail\Form;

use Drupal\Core\Entity\ContentEntityDeleteForm;


class ContactMailDeleteForm extends ContentEntityDeleteForm
{

  /**
   * {@inheritdoc}
   */
  public function getQuestion()
  {
    return "このお問い合わせ{$this->entity->label()}を削除してもよろしいですか？";
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl()
  {
    return $this->entity->toUrl('collection');
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText()
  {
    return '削除';
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription()
  {
    return 'この操作は元に戻せません。';
  }
}
