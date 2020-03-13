<?php

/*
 * This file is part of Flarum.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace Flarum\Auth\Twitter;

use Flarum\Forum\Auth\SsoDriverInterface;
use Flarum\Forum\Auth\SsoResponse;
use Flarum\Http\UrlGenerator;
use Flarum\Settings\SettingsRepositoryInterface;
use Laminas\Diactoros\Response\RedirectResponse;
use League\OAuth1\Client\Server\Twitter;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Symfony\Component\Translation\TranslatorInterface;

class TwitterAuthDriver implements SsoDriverInterface
{
    /**
     * @var SettingsRepositoryInterface
     */
    protected $settings;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var UrlGenerator
     */
    protected $url;

    /**
     * @param SettingsRepositoryInterface $settings
     * @param TranslatorInterface $translator
     * @param UrlGenerator $url
     */
    public function __construct(SettingsRepositoryInterface $settings, TranslatorInterface $translator, UrlGenerator $url)
    {
        $this->settings = $settings;
        $this->translator = $translator;
        $this->url = $url;
    }

    public function meta(): array
    {
        return [
            "name" => "Twitter",
            "icon" => "fab fa-twitter",
            "buttonColor" => "#55ADEE",
            "buttonText" => $this->translator->trans('flarum-auth-twitter.forum.log_in.with_twitter_button'),
            "buttonTextColor" => "#fff",
        ];
    }

    /**
     * @param Request $request
     * @return ResponseInterface
     */
    public function sso(Request $request, SsoResponse $ssoResponse)
    {
        $redirectUri = $this->url->to('forum')->route('sso', ['provider' => 'twitter']);

        $server = new Twitter([
            'identifier' => $this->settings->get('flarum-auth-twitter.api_key'),
            'secret' => $this->settings->get('flarum-auth-twitter.api_secret'),
            'callback_uri' => $redirectUri,
            'isSecure' => false,
        ]);

        $session = $request->getAttribute('session');

        $queryParams = $request->getQueryParams();
        $oAuthToken = array_get($queryParams, 'oauth_token');
        $oAuthVerifier = array_get($queryParams, 'oauth_verifier');

        if (! $oAuthToken || ! $oAuthVerifier) {
            $temporaryCredentials = $server->getTemporaryCredentials();

            $session->put('temporary_credentials', serialize($temporaryCredentials));

            $authUrl = $server->getAuthorizationUrl($temporaryCredentials);

            return new RedirectResponse($authUrl);
        }

        $temporaryCredentials = unserialize($session->get('temporary_credentials'));

        $tokenCredentials = $server->getTokenCredentials($temporaryCredentials, $oAuthToken, $oAuthVerifier);

        $user = $server->getUserDetails($tokenCredentials);

        return $ssoResponse
            ->withIdentifier($user->uid)
            ->provideTrustedEmail($user->email)
            ->provideAvatar(str_replace('_normal', '', $user->imageUrl))
            ->suggestUsername($user->nickname)
            ->setPayload(get_object_vars($user));
    }
}
