<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdminUserFixture extends Fixture
{
    public function __construct(
        private readonly UserRepository $users,
        private readonly UserPasswordHasherInterface $passwordHasher,
        #[Autowire('%env(resolve:ADMIN_EMAIL)%')]
        private readonly string $adminEmail,
        #[Autowire('%env(resolve:ADMIN_PASSWORD)%')]
        private readonly string $adminPassword,
    ) {}

    public function load(ObjectManager $manager): void
    {
        $existing = $this->users->findOneBy(['email' => $this->adminEmail]);
        if ($existing instanceof User) {
            $existing->setRoles(['ROLE_ADMIN']);
            $manager->flush();
            return;
        }

        $user = new User();
        $user->setEmail($this->adminEmail);

        $user->setPassword(
            $this->passwordHasher->hashPassword($user, $this->adminPassword)
        );

        $user->setRoles(['ROLE_ADMIN']);

        $manager->persist($user);
        $manager->flush();
    }
}
