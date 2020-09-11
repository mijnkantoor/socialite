<?php

namespace MijnKantoor\Socialite;

use Illuminate\Support\Arr;
use SocialiteProviders\Manager\OAuth2\AbstractProvider;
use SocialiteProviders\Manager\OAuth2\User;

class Provider extends AbstractProvider
{
    /**
     * Unique Provider Identifier.
     */
    public const IDENTIFIER = 'MIJNKANTOORAPP';

    /**
     * {@inheritdoc}
     */
    protected $scopes = [''];

    /**
     * {@inheritdoc}
     */
    protected $scopeSeparator = ' ';

    /**
     * {@inheritdoc}
     */
    public static function additionalConfigKeys()
    {
        return [
            'host',
            'staging',
            'authorize_uri',
            'token_uri',
            'userinfo_uri',
            'userinfo_key',
            'user_id',
            'user_nickname',
            'user_name',
            'user_email',
            'user_avatar',
        ];
    }

    /**
     * Get the authentication URL for the provider.
     *
     * @param string $state
     *
     * @return string
     */
    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase($this->getMijnKantoorappUrl('authorize_uri'), $state);
    }

    /**
     * @param $type
     * @return string
     */
    protected function getMijnKantoorappUrl($type)
    {
        $host = $this->getConfig('host')
            ?? (bool)$this->getConfig('staging', false)
                ? 'https://api.staging.mijnkantoorapp.nl'
                : 'https://api.mijnkantoorapp.nl';

        return rtrim($host, '/') . '/' . ltrim(($this->getConfig($type, Arr::get([
                'authorize_uri' => 'v1/oauth/authorize',
                'token_uri' => 'v1/oauth/token',
                'userinfo_uri' => 'v1/me',
            ], $type))), '/');
    }

    /**
     * Get the token URL for the provider.
     *
     * @return string
     */
    protected function getTokenUrl()
    {
        return $this->getMijnKantoorappUrl('token_uri');
    }

    /**
     * Get the raw user for the given access token.
     *
     * @param string $token
     *
     * @return array
     */
    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get($this->getMijnKantoorappUrl('userinfo_uri'), [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);

        return (array)json_decode($response->getBody(), true);
    }

    /**
     * Map the raw user array to a Socialite User instance.
     *
     * @param array $user
     *
     * @return \Laravel\Socialite\User
     */
    protected function mapUserToObject(array $user)
    {
        $key = $this->getConfig('userinfo_key', null);
        $data = is_null($key) === true ? $user : Arr::get($user, $key, []);

        //String data key
        $data = $data['data'] ?? $data;

        return (new User())->setRaw($data)->map([
            'id' => $this->getUserData($data, 'id'),
//            'nickname' => $this->getUserData($data, 'nickname'),
            'name' => $this->getUserData($data, 'first_name'),
            'email' => $this->getUserData($data, 'email'),
//            'avatar' => $this->getUserData($data, 'avatar'),
        ]);
    }

    /**
     * @param $user
     * @param $key
     * @return array|\ArrayAccess|mixed
     */
    protected function getUserData($user, $key)
    {
        return Arr::get($user, $this->getConfig('user_' . $key, $key));
    }

    /**
     * Get the POST fields for the token request.
     *
     * @param string $code
     *
     * @return array
     */
    protected function getTokenFields($code)
    {
        return array_merge(parent::getTokenFields($code), [
            'grant_type' => 'authorization_code',
        ]);
    }
}
