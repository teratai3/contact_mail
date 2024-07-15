<?php
namespace Drupal\contact_mail\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class KanaConstraintValidator extends ConstraintValidator {

  public function validate($value, Constraint $constraint) {
    // 値がオブジェクトの場合、$value->value から値を取得
    if (is_object($value)) {
      $value = $value?->value;
    }

    

    if (!preg_match('/^[ァ-ヶー]+$/u',$value)) {
      $this->context->buildViolation($constraint->notKana)
        ->addViolation();
    }
  }
}
