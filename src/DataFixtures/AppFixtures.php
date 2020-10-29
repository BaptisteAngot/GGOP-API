<?php

namespace App\DataFixtures;

use App\Entity\Ban;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder) {
        $this->passwordEncoder = $passwordEncoder;
    }
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user
            ->setPseudo("tootoo")
            ->setEmail("tootoo@tootoo.com")
            ->setRiotPseudo("tootoo")
            ->setPassword($this->passwordEncoder->encodePassword(
                $user,
                'tootoo'
            ));
        $manager->persist($user);

        $ban = new Ban();
        $ban
            ->setMotive("NaN")
            ->setStart(new \DateTime())
            ->setEnd(new \DateTime())
            ->setUserId($user);
        $manager->persist($ban);

        $manager->flush();
    }
}
