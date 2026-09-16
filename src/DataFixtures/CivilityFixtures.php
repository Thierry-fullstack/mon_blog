<?php

namespace App\DataFixtures;

use App\Entity\Civility;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CivilityFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $civility = new Civility();
        $civility->setGender('woman');
        $manager->persist($civility);
        $civility = new Civility();
        $civility->setGender('man');
        $manager->persist($civility);
        $manager->flush();
    }
}
