<?php

namespace App\Validator\Constraints;

use App\Entity\Customer;
use App\Entity\Producer;
use App\Repository\UserRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Class UpdateEmailAvailableValidator
 * @package App\Validator\Constraints
 */
class UpdateEmailAvailableValidator extends ConstraintValidator
{
    /** @var UserRepository $userRepository */
    private $userRepository;

    /** @var Security $security */
    private $security;

    public function __construct(
        UserRepository $userRepository,
        Security $security
    ) {
        $this->userRepository = $userRepository;
        $this->security = $security;
    }

    /**
     * @param mixed $value
     * @param Constraint $constraint
     * @throws NonUniqueResultException
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof UpdateEmailAvailable) {
            throw new UnexpectedTypeException($constraint, $value);
        }
        if ($value === null || $value === '') {
            return;
        }
        /** @var Customer|Producer|UserInterface|null $user */
        $user = $this->security->getUser();

        if (!$this->isValidEmail($value, $user->getEmail())) {
            $this->context
                ->buildViolation($constraint->message)
                ->atPath('email')
                ->addViolation();
        }
    }

    /**
     * @param string $email
     * @param string $excludedEmail
     * @return bool
     * @throws NonUniqueResultException
     */
    private function isValidEmail(string $email, string $excludedEmail): bool
    {
        $user = $this->userRepository->findUserByEmailExcludeCurrentUserEmail($email, $excludedEmail);
        return is_null($user);
    }
}
