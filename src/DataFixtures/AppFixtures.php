<?php

namespace App\DataFixtures;

use App\Entity\Producer;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    /**
     * @var UserPasswordEncoderInterface $passwordEncoder
     */
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 20; $i++) {
            $product = new Product();
            $product->setName('product ' . $i);
            $manager->persist($product);
        }

        $user = new Producer();
        $user->setFirstName("John");
        $user->setLastName("Doe");
        $user->setEmail("johndoe@email.fr");
        $user->setPassword($this->passwordEncoder->encodePassword($user, '123456'));
        $manager->persist($user);


        $manager->flush();
    }
}
