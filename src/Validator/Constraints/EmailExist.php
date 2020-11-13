<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class EmailExist
 * @package App\Validator\Constraints
 * @Annotation
 */
class EmailExist extends Constraint
{
    public $message = "Cette adresse email n'existe pas";
}
