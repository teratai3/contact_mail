<?php

namespace Drupal\contact_mail\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * Checks that the submitted value is only Katakana.
 *
 * @Constraint(
 *   id = "KanaConstraint",
 *   label = @Translation("カタカナで入力", context = "Validation"),
 *   type = "string"
 * )
 */
class KanaConstraint extends Constraint {
  public $notKana = 'カタカナで入力してください';

  public function validatedBy() {
    return static::class . 'Validator';
  }
}
