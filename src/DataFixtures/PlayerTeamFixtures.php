<?php


namespace App\DataFixtures;

use App\Document\Team\Team;
use App\Document\Team\TeamPlayer;

use Doctrine\Bundle\MongoDBBundle\Fixture\Fixture;
use Doctrine\Persistence\ObjectManager;

use Faker;

class PlayerTeamFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        //Création de 6 équipes
        for ($i = 0; $i < 6; $i++) {
            $faker = Faker\Factory::create('FR-fr');
            $team = new Team();
            $team->setName($faker->name('lastName'));
            $team->setCreatorId(rand(0,100));
            $team->setIsComplete(false);
            $team->setWinRate(rand(0,100));

            for ($k=0;$k < 5; $k++) {
                $player = new TeamPlayer();
                $player->setUserId(rand(0,100));
                $player->setStatus('EN ATTENTE');
                $player->setUserPseudo($faker->name('lastname'));
                $team->addPlayers($player);
            }
            $manager->persist($team);
        }
        $manager->flush();
    }
}