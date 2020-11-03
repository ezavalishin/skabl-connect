<?php

namespace ezavalishin\SkablConnect;

class UserResponse
{
    /**
     * @var int
     */
    public $id;
    /**
     * @var string
     */
    public $email;
    /**
     * @var string
     */
    public $avatar;

    /**
     * @var string|null
     */
    public $nickname;
    /**
     * @var string|null
     */
    public $firstName;
    /**
     * @var string|null
     */
    public $lastName;

    /**
     * @var bool
     */
    public $emailIsVerified;

    public function __construct(array $attributes)
    {
        $this->id = $attributes['id'];
        $this->email = $attributes['email'];
        $this->avatar = $attributes['avatar'];

        $this->nickname = $attributes['nickname'] ?? null;
        $this->firstName = $attributes['firstName'] ?? null;
        $this->lastName = $attributes['lastName'] ?? null;

        $this->emailIsVerified = $attributes['emailIsVerified'];
    }
}
