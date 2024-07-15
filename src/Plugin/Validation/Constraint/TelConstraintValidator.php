<?php

namespace Drupal\contact_mail\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class TelConstraintValidator extends ConstraintValidator
{
  public function validate($value, Constraint $constraint)
  {

    // 値がオブジェクトの場合、$value->value から値を取得
    if (is_object($value)) {
      $value = $value?->value;
    }

  
    if (!is_null($value) && !preg_match('/^\d{2,4}-\d{2,4}-\d{4}$/', $value)) {
      $this->context->buildViolation($constraint->invalidPhone)->addViolation();
    }
  }
}
