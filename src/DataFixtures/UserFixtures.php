<?php

namespace App\DataFixtures;

use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class UserFixtures extends Fixture
{
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher){}

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $user = new User();
        $user->setRoles(['ROLE_ADMIN'])->setCreatedAt(new DateTimeImmutable())->setEmail($faker->email())->setRgpd(true);
        $user->setPassword($this->passwordHasher->hashPassword($user,'ArethiA75!'));
        $manager->persist($user);

        for($i =0; $i < 5; $i ++){
            $user = new User();
            $user->setRoles(['ROLE_AUTHOR'])->setCreatedAt(new DateTimeImmutable())->setEmail($faker->email())->setRgpd(true);
            $user->setPassword($this->passwordHasher->hashPassword($user,'ArethiA75!'));
            $manager->persist($user);
        }

        $user = new User();
        $user->setRoles(['ROLE_USER'])->setCreatedAt(new DateTimeImmutable())->setEmail($faker->email())->setRgpd(true);
        $user->setPassword($this->passwordHasher->hashPassword($user,'ArethiA75!'));
        $manager->persist($user);


        $manager->flush();
    }
}
