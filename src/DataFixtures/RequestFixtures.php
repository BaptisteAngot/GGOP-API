<?php


namespace App\DataFixtures;

use Doctrine\Bundle\MongoDBBundle\Fixture\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Document\Request\RequestGGOP;


class RequestFixtures extends Fixture
{

    public function load(ObjectManager $manager)
    {
        $requestType = ['INVITE_TEAM','INVITE_MATCHMAKING','REQUEST_MATCHMAKING','REQUEST_TOURNAMENT'];

        for ($i = 0; $i < 50; $i++) {
            $request = new RequestGGOP();
            $request->setType($requestType[rand(0,3)]);
            $request->setFrom(rand(0,100));
            $request->setTo(rand(0,100));
            $request->setRequestValue(rand(0,100));
            $manager->persist($request);
        }
        $manager->flush();
    }

}