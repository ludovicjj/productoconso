<?php

namespace App\Validator\Constraints;

use App\DTO\RegistrationDTO;
use App\Repository\UserRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class EmailAvailableValidator extends ConstraintValidator
{
    /**
     * @var UserRepository $userRepository
     */
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param mixed $DTO
     * @param Constraint $constraint
     */
    public function validate($DTO, Constraint $constraint)
    {
        if (!$constraint instanceof EmailAvailable) {
            throw new UnexpectedTypeException($constraint, $DTO);
        }

        if (!$this->isValidEmail($DTO)) {
            $this->context
                ->buildViolation($constraint->message)
                ->atPath('email')
                ->addViolation();
        }
    }

    private function isValidEmail(RegistrationDTO $DTO): bool
    {
        $user = $this->userRepository->findOneBy(['email' => $DTO->getEmail()]);

        return is_null($user);
    }
}
