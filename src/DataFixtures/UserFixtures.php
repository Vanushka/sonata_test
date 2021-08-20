<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordEncoderInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setUsername("Admin");
        $user->setEmail('admin@admin.com');
        $user->setPassword($this->passwordHasher->encodePassword(
            $user,
            'password'
        ));
        $user->setActive(1);
        $user->setRoles(array('ROLE_ADMIN'));
        $user->setCreatedAt(new \DateTimeImmutable());
        $user->setUpdatedAt(new \DateTimeImmutable());

        $manager->persist($user);
        $manager->flush();

        $user = new User();
        $user->setUsername("Andrey");
        $user->setEmail('andrey@mail.com');
        $user->setPassword($this->passwordHasher->encodePassword(
            $user,
            'secret'
        ));
        $user->setActive(1);
        $user->setRoles(array('ROLE_MANAGER'));
        $user->setCreatedAt(new \DateTimeImmutable());
        $user->setUpdatedAt(new \DateTimeImmutable());

        $manager->persist($user);
        $manager->flush();
    }
}
