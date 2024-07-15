<?php

namespace Drupal\contact_mail\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * Checks that the submitted value is a valid phone number.
 *
 * @Constraint(
 *   id = "TelConstraint",
 *   label = @Translation("電話番号を入力", context = "Validation"),
 *   type = "string"
 * )
 */
class TelConstraint extends Constraint {
  public $invalidPhone = 'ハイフン付きで電話番号を入力してください';

  public function validatedBy() {
    return static::class . 'Validator';
  }
}
