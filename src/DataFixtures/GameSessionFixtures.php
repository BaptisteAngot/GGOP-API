<?php


namespace App\DataFixtures;

use App\Document\GameSession\GameSession;
use App\Document\GameSession\GameSessionTeam;
use App\Document\GameSession\Player;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\MongoDBBundle\Fixture\Fixture;

use Faker;

class GameSessionFixtures extends Fixture
{

    public function load(ObjectManager $manager)
    {
        for($i = 0; $i < 10; $i++) {
            $faker = Faker\Factory::create('FR-fr');

            $gameSession = new GameSession();
            $gameSession->setCreatorId(rand(0,1000));
            $gameSession->setDateDebut($faker->date());
            $gameSession->setDateFin($faker->date());

            $team = new GameSessionTeam();
            $team->setWinrate(rand(0,100));

            for ($k = 0; $k < 5; $k++) {
                $player = new Player();
                $player->setUserPseudo($faker->name('lastName'));
                $player->setStatus('EN ATTENTE');
                $player->setUserId(rand(0,100));
                $team->addPlayers($player);
            }
            $gameSession->addTeam($team);
            $manager->persist($gameSession);
        }
        $manager->flush();
    }
}