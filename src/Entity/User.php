<?php

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class User
 * @package App\Entity
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({"producer"="App\Entity\Producer", "customer"="App\Entity\Customer"})
 *
 * https://www.doctrine-project.org/projects/doctrine-orm/en/2.7/reference/inheritance-mapping.html
 */
abstract class User implements UserInterface
{
    /**
     * @var UuidInterface $id
     * @ORM\Id
     * @ORM\Column(type="uuid")
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class=UuidGenerator::class)
     */
    protected $id;

    /**
     * @var string $firstName
     * @ORM\Column(type="string")
     */
    protected $firstName;

    /**
     * @var string $lastName
     * @ORM\Column(type="string")
     */
    protected $lastName;

    /**
     * @var string $email
     * @ORM\Column(type="string", unique=true)
     */
    protected $email;

    /**
     * @var string $password
     * @ORM\Column(type="string")
     */
    protected $password;

    /**
     * @var string|null $plainPassword
     * @ORM\Column(type="string", nullable=true)
     */
    protected $plainPassword = null;

    /**
     * @var DateTimeImmutable $registerAt
     * @ORM\Column(type="datetime_immutable")
     */
    protected $registerAt;

    /**
     * @var null|ForgottenPassword $forgottenPassword
     * @ORM\Embedded(class="ForgottenPassword")
     */
    protected $forgottenPassword;

    public function __construct()
    {
        $this->registerAt = new DateTimeImmutable();
    }

    /**
     * @return UuidInterface
     */
    public function getId(): UuidInterface
    {
        return $this->id;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string|null $plainPassword
     */
    public function setPlainPassword(?string $plainPassword)
    {
        $this->plainPassword = $plainPassword;
    }

    /**
     * @return string|null
     */
    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    /**
     * @param DateTimeImmutable $dateTimeImmutable
     */
    public function setRegisterAt(DateTimeImmutable $dateTimeImmutable): void
    {
        $this->registerAt = $dateTimeImmutable;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getRegisterAt(): DateTimeImmutable
    {
        return $this->registerAt;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->email;
    }

    /**
     * @return ForgottenPassword|null
     */
    public function getForgottenPassword(): ?ForgottenPassword
    {
        return $this->forgottenPassword;
    }

    public function hasForgottenPassword(): void
    {
        $this->forgottenPassword = new ForgottenPassword();
    }

    public function getSalt(): void
    {
    }

    public function eraseCredentials()
    {
    }
}
