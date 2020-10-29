<?php


namespace App\DataFixtures;

use App\Entity\RiotServer;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;


class RiotServerFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $serverEuw = new RiotServer();
        $serverEuw
            ->setName("Europe West 1")
            ->setApiRoute("EUW1");
        $manager->persist($serverEuw);

        $serverBr = new RiotServer();
        $serverBr
            ->setName("Brazil")
            ->setApiRoute("BR1");
        $manager->persist($serverBr);

        $serverEun = new RiotServer();
        $serverEun
            ->setName("Europe Nordic & East")
            ->setApiRoute("EUN1");
        $manager->persist($serverEun);

        $serverJp = new RiotServer();
        $serverJp
            ->setName("Japan")
            ->setApiRoute("JP1");
        $manager->persist($serverJp);

        $serverTr = new RiotServer();
        $serverTr
            ->setName("Turkey")
            ->setApiRoute("TR1");
        $manager->persist($serverTr);

        $serverLAN = new RiotServer();
        $serverLAN
            ->setName("Latin America North")
            ->setApiRoute("LA1");
        $manager->persist($serverLAN);

        $serverLAS = new RiotServer();
        $serverLAS
            ->setName("Latin America South")
            ->setApiRoute("LA2");
        $manager->persist($serverLAS);

        $serverNa = new RiotServer();
        $serverNa
            ->setName("North America")
            ->setApiRoute("NA1");
        $manager->persist($serverNa);

        $serverOce = new RiotServer();
        $serverOce
            ->setName("Oceania")
            ->setApiRoute("OC1");
        $manager->persist($serverOce);

        $serverRu = new RiotServer();
        $serverRu
            ->setName("Russia")
            ->setApiRoute("RU1");
        $manager->persist($serverRu);

        $serverRK = new RiotServer();
        $serverRK
            ->setName("Republic of Korea")
            ->setApiRoute("KR");
        $manager->persist($serverRK);

        $serverBeta = new RiotServer();
        $serverBeta
            ->setName("Public Beta Environment")
            ->setApiRoute("PBE");
        $manager->persist($serverBeta);

        $manager->flush();
    }
}