<?php

namespace App\Services;

use App\Models\Token;
use Carbon\Carbon;
use Patreon\OAuth;

class TokenService
{
    /**
     * @var OAuth
     */
    private $oauthClient;

    public function __construct()
    {
        $this->oauthClient = new OAuth(
            config('patreon.client_id'),
            config('patreon.client_secret')
        );
    }

    /**
     * Create a token using config values and refresh it
     */
    public function initializeToken(): Token
    {
        // This allows me to initialize the token with config values if it doesn't exist
        // or I need to update the token with new values from config
        $token = Token::first() ?? new Token;
        $token->access = config('patreon.initial_access_token');
        $token->refresh = config('patreon.initial_refresh_token');
        $token->expires_in = '';
        $token->expires = Carbon::now()->addSeconds(10);
        $token->save();

        // Refresh the token right away
        [$token] = $this->refreshToken($token, true);

        return $token;
    }

    /**
     * Get a token
     *
     * @throws \RuntimeException
     */
    public function getToken(): Token
    {
        $token = Token::first();

        if (! $token) {
            throw new \RuntimeException('No token found. Please create a token first.');
        }

        return $token;
    }

    /**
     * Refresh the token using Patreon SDK
     *
     * @param  Token|null  $token  The token to refresh
     * @param  bool  $force  Whether to force refresh the token
     * @return array{token: Token, refreshed: bool}
     *
     * @throws \RuntimeException
     */
    public function refreshToken(?Token $token = null, bool $force = false): array
    {
        $token = $token ?? $this->getToken();
        $refreshed = false;

        if ($this->isTokenExpired($token) || $force) {
            try {
                $tokens = $this->oauthClient->refresh_token($token->refresh, null);

                if (! isset($tokens['access_token'])) {
                    throw new \RuntimeException('Failed to refresh token: No access token in response');
                }

                $token->access = $tokens['access_token'];
                $token->refresh = $tokens['refresh_token'];
                $token->expires_in = $tokens['expires_in'];
                $token->expires = Carbon::now()->addSeconds($tokens['expires_in']);
                $token->save();

                $refreshed = true;
            } catch (\Exception $e) {
                throw new \RuntimeException('Failed to refresh token: '.$e->getMessage());
            }
        }

        return [$token, $refreshed];
    }

    /**
     * Check if token is expired
     */
    private function isTokenExpired(Token $token): bool
    {
        return Carbon::now() > $token->expires;
    }
}
