<?php

namespace App\Validator\Constraints;

use App\Repository\UserRepository;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class EmailExistValidator extends ConstraintValidator
{
    /**
     * @var UserRepository $userRepository
     */
    private $userRepository;

    /**
     * EmailExistValidator constructor.
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof EmailExist) {
            throw new UnexpectedTypeException($constraint, $value);
        }

        if ($value === null || $value === '') {
            return;
        }

        $user = $this->userRepository->findOneBy(['email' => $value]);

        if (is_null($user)) {
            $this->context
                ->buildViolation($constraint->message)
                ->atPath('email')
                ->addViolation();
        }
    }
}
