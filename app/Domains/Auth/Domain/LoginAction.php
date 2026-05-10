<?php

declare(strict_types=1);

namespace App\Domains\Auth\Domain;

use App\Domains\Auth\Data\LoginData;
use App\Domains\Auth\Data\LoginResult;
use App\Domains\Auth\Domain\Contracts\UserRepositoryInterface;
use App\Domains\Auth\Exceptions\InvalidCredentialsException;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\Client;
use Laravel\Passport\Http\Controllers\AccessTokenController;
use Illuminate\Http\Request;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;
use GuzzleHttp\Psr7\HttpFactory;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;

final readonly class LoginAction
{
    private string $passportClientId;
    private string $passportClientSecret;

    public function __construct(
        private UserRepositoryInterface $repository,
        private AuthorizationServer     $server,
    ) {
        $this->passportClientId = (string) config('passport.password_client_id');
        $this->passportClientSecret = (string) config('passport.password_client_secret');
    }

    public function execute(LoginData $data): LoginResult
    {
        // Validate credentials — fail fast
        $user = $this->repository->findByEmail($data->email);

        if (!$user || !Hash::check($data->password, $user->password)) {
            throw new InvalidCredentialsException();
        }

        // Build a Laravel Request and convert to PSR-7 via Symfony bridge
        $laravelRequest = Request::create('/oauth/token', 'POST', [
            'grant_type'    => 'password',
            'client_id'     => $this->passportClientId,
            'client_secret' => $this->passportClientSecret,
            'username'      => $data->email,
            'password'      => $data->password,
            'scope'         => '',
        ]);

        $httpFactory = new HttpFactory();
        $psrFactory  = new PsrHttpFactory($httpFactory, $httpFactory, $httpFactory, $httpFactory);
        $psrRequest  = $psrFactory->createRequest($laravelRequest);

        // Issue tokens via OAuth2 server directly (no HTTP round-trip)
        try {
            $psrResponse = $this->server->respondToAccessTokenRequest(
                $psrRequest,
                $httpFactory->createResponse(),
            );
        } catch (OAuthServerException $e) {
            \Illuminate\Support\Facades\Log::error('Passport OAuthServerException: ' . $e->getMessage(), [
                'hint'        => $e->getHint(),
                'client_id'   => $this->passportClientId,
            ]);
            throw new InvalidCredentialsException();
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Passport unexpected error: ' . $e->getMessage(), [
                'class' => get_class($e),
                'trace' => $e->getTraceAsString(),
            ]);
            throw new InvalidCredentialsException();
        }

        $tokens = json_decode((string) $psrResponse->getBody(), true);

        return new LoginResult(
            user:         $user,
            accessToken:  $tokens['access_token'],
            refreshToken: $tokens['refresh_token'],
            expiresIn:    $tokens['expires_in'],
        );
    }
}