<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class UpdateEmailAvailable
 * @package App\Validator\Constraints
 * @Annotation
 */
class UpdateEmailAvailable extends Constraint
{
    /** @var string $message */
    public $message = "Cette adresse email est déjà utilisé.";
}
