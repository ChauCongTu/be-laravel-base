<?php

declare(strict_types=1);

namespace App\Domains\Auth\Domain;

use App\Domains\Auth\Data\LoginResult;
use App\Domains\Auth\Data\RefreshTokenData;
use App\Domains\Auth\Domain\Contracts\UserRepositoryInterface;
use App\Domains\Auth\Exceptions\InvalidRefreshTokenException;
use GuzzleHttp\Psr7\HttpFactory;
use Illuminate\Http\Request;
use Laravel\Passport\Client;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;

final readonly class RefreshTokenAction
{
    public function __construct(
        private UserRepositoryInterface $repository,
        private AuthorizationServer     $server,
    ) {}

    public function execute(RefreshTokenData $data): LoginResult
    {
        $client = Client::where('grant_types', 'like', '%password%')
            ->where('revoked', false)
            ->firstOrFail();

        $laravelRequest = Request::create('/oauth/token', 'POST', [
            'grant_type'    => 'refresh_token',
            'refresh_token' => $data->refreshToken,
            'client_id'     => $client->id,
            'client_secret' => $client->secret,
            'scope'         => '',
        ]);

        $httpFactory = new HttpFactory();
        $psrFactory  = new PsrHttpFactory($httpFactory, $httpFactory, $httpFactory, $httpFactory);
        $psrRequest  = $psrFactory->createRequest($laravelRequest);

        try {
            $psrResponse = $this->server->respondToAccessTokenRequest(
                $psrRequest,
                $httpFactory->createResponse(),
            );
        } catch (OAuthServerException $e) {
            throw new InvalidRefreshTokenException();
        }

        $tokens = json_decode((string) $psrResponse->getBody(), true);

        // Decode JWT payload to get user ID
        $parts   = explode('.', $tokens['access_token']);
        $payload = json_decode(base64_decode(strtr($parts[1], '-_', '+/')), true);
        $user    = $this->repository->findById((int) $payload['sub']);

        if (!$user) {
            throw new InvalidRefreshTokenException();
        }

        return new LoginResult(
            user:         $user,
            accessToken:  $tokens['access_token'],
            refreshToken: $tokens['refresh_token'],
            expiresIn:    $tokens['expires_in'],
        );
    }
}
