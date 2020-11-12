<?php

namespace ezavalishin\SkablConnect;

use ezavalishin\SkablConnect\Exceptions\AccessTokenRequired;
use ezavalishin\SkablConnect\Exceptions\InvalidAccessToken;
use ezavalishin\SkablConnect\Exceptions\InvalidCredentials;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Validation\ValidationException;

class SkablConnect
{
    /**
     * @var int
     */
    private $clientId;

    /**
     * @var string
     */
    private $clientSecret;

    /**
     * @var Client
     */
    private $client;

    /**
     * @var string
     */
    private $accessToken;

    public function __construct(string $url, int $clientId, string $clientSecret)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;

        $this->client = new Client([
            'base_uri' => $url
        ]);
    }

    /**
     * @param string $username
     * @param string $password
     * @param string $scope
     * @return LoginResponse
     * @throws GuzzleException
     * @throws InvalidCredentials
     * @throws \JsonException
     */
    public function makeLogin(string $username, string $password, string $scope = ''): LoginResponse
    {
        try {
            $response = $this->client->post('/oauth/token', [
                'json' => [
                    'grant_type' => 'password',
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'scope' => $scope,
                    'username' => $username,
                    'password' => $password
                ]
            ]);
        } catch (GuzzleException $e) {
            if (($e instanceof RequestException) && ($response = $e->getResponse()) && $response->getStatusCode() === 400) {
                throw new InvalidCredentials('invalid credentials', $e->getCode(), $e);
            }

            throw $e;
        }

        $loginResponse = new LoginResponse(json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR));;

        $this->accessToken = $loginResponse->accessToken;

        return $loginResponse;
    }

    /**
     * @param string $username
     * @param string $password
     * @return bool
     * @throws GuzzleException
     * @throws ValidationException
     * @throws \JsonException
     */
    public function makeRegister(string $username, string $password): bool
    {
        try {
            $this->client->post('/api/register', [
                'headers' => [
                    'Accept' => 'application/json',
                ],
                'json' => [
                    'email' => $username,
                    'password' => $password
                ]
            ]);
        } catch (GuzzleException $e) {
            if ($e instanceof RequestException && ($response = $e->getResponse()) && $response->getStatusCode() === 422) {
                $response = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

                throw ValidationException::withMessages($response['errors']);
            }

            throw $e;
        }

        return true;
    }

    public function setAccessToken(string $accessToken): SkablConnect
    {
        $this->accessToken = $accessToken;
        return $this;
    }

    /**
     * @return UserResponse
     * @throws AccessTokenRequired
     * @throws GuzzleException
     * @throws InvalidAccessToken
     * @throws \JsonException
     */
    public function getUser(): UserResponse
    {
        if (!isset($this->accessToken)) {
            throw new AccessTokenRequired('access token required');
        }

        try {
            $response = $this->client->get('/api/user', [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->accessToken
                ]
            ]);
        } catch (GuzzleException $e) {
            if ($e instanceof RequestException && ($response = $e->getResponse()) && $response->getStatusCode() === 401) {
                throw new InvalidAccessToken('invalid access token');
            }

            throw $e;
        }

        return new UserResponse(json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR)['data']);
    }

    /**
     * @param array $attributes
     * @return bool
     * @throws AccessTokenRequired
     * @throws GuzzleException
     * @throws InvalidAccessToken
     * @throws ValidationException
     * @throws \JsonException
     */
    public function updateUser(array $attributes): bool
    {
        if (!isset($this->accessToken)) {
            throw new AccessTokenRequired('access token required');
        }

        try {
            $this->client->put('/api/user', [
                'json' => $attributes,
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->accessToken
                ]
            ]);
        } catch (GuzzleException $e) {
            if ($e instanceof RequestException && ($response = $e->getResponse()) && $response->getStatusCode() === 401) {
                throw new InvalidAccessToken('invalid access token');
            }

            if ($e instanceof RequestException && ($response = $e->getResponse()) && $response->getStatusCode() === 422) {
                $response = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

                throw ValidationException::withMessages($response['errors']);
            }

            throw $e;
        }

        return true;
    }

    /**
     * @param string $email
     * @param string $password
     * @return UserResponse
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function linkUser(string $email, string $password): UserResponse
    {
        $response = $this->client->post('/api/link', [
            'headers' => [
                'Accept' => 'application/json',
            ],
            'json' => [
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'email' => $email,
                'password' => $password
            ]
        ]);

        return new UserResponse(json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR)['data']);
    }
}
