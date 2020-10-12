<?php

namespace App\DataFixtures;

use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\Name;
use App\Model\User\Entity\User\Role;
use App\Model\User\Entity\User\User;
use App\Model\User\Entity\User\Id;
use App\Model\User\Service\PasswordHasher;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixture extends Fixture
{
    public const REFERENCE_ADMIN = 'user_user_admin';
    public const REFERENCE_OPERATOR = 'user_user_operator';
    public const REFERENCE_USER = 'user_user_user';

    private PasswordHasher $hasher;

    public function __construct(PasswordHasher $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $hash = $this->hasher->hash('password');

        $user = $this->createByEmail(
            new Name('User', 'User'),
            new Email('user@app.test'),
            $hash
        );
        $manager->persist($user);
        $this->setReference(self::REFERENCE_USER, $user);

        $operator = $this->createOperatorByEmail(
            new Name('Operator', 'Operator'),
            new Email('operator@app.test'),
            $hash
        );
        $manager->persist($operator);
        $this->setReference(self::REFERENCE_OPERATOR, $operator);

        $admin = $this->createAdminByEmail(
            new Name('Admin', 'Admin'),
            new Email('admin@app.test'),
            $hash
        );
        $manager->persist($admin);
        $this->setReference(self::REFERENCE_ADMIN, $admin);

        $manager->flush();
    }

    private function createAdminByEmail(Name $name, Email $email, string $hash): User
    {
        $user = $this->createByEmail($name, $email, $hash);
        $user->changeRole(Role::admin());
        return $user;
    }

    private function createOperatorByEmail(Name $name, Email $email, string $hash): User
    {
        $user = $this->createByEmail($name, $email, $hash);
        $user->changeRole(Role::operator());
        return $user;
    }

    private function createByEmail(Name $name, Email $email, string $hash): User
    {
        return User::signUpByEmail(
            Id::next(),
            new \DateTimeImmutable(),
            $name,
            $email,
            $hash
        );
    }

}
