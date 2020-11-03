<?php

namespace ezavalishin\SkablConnect;

class LoginResponse
{
    /**
     * @var string
     */
    public $accessToken;

    /**
     * @var string
     */
    public $refreshToken;

    /**
     * @var string
     */
    public $tokenType;

    /**
     * @var int
     */
    public $expiresIn;

    public function __construct(array $attributes)
    {
        $this->accessToken = $attributes['access_token'];
        $this->refreshToken = $attributes['refresh_token'];
        $this->tokenType = $attributes['token_type'];
        $this->expiresIn = $attributes['expires_in'];
    }
}
