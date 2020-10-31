<?php


namespace App\DataFixtures;

use App\Document\Report\Report;
use Doctrine\Bundle\MongoDBBundle\Fixture\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;

class ReportFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('FR-fr');
        $report = new Report();
        $report->setDescription($faker->text);
        $report->setFromUser(13);
        $report->setForUser(12);
        $manager->persist($report);
        $manager->flush();
    }
}