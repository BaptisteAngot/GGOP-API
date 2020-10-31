<?php


namespace App\DataFixtures;


use App\Document\Test;
use Doctrine\Bundle\MongoDBBundle\Fixture\Fixture;
use Doctrine\Persistence\ObjectManager;

class TestFixtures extends Fixture
{

    public function load(ObjectManager $manager)
    {
        $test = new Test();
        $test->setName("toto");
        $manager->persist($test);
        $manager->flush();
    }
}