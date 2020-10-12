<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\SignUp\Request;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\NotBlank()
     */
    public string $firstName;
    /**
     * @Assert\NotBlank()
     */
    public string $lastName;
    /**
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    public string $email;
    /**
     * @Assert\NotBlank()
     * @Assert\Length(min=6)
     */
    public string $password;
}
