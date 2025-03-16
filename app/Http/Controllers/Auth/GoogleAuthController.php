<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Google\Exception;
use Illuminate\Http\Request;
use Google\Client;
use Illuminate\Support\Facades\Session;

class GoogleAuthController extends Controller
{
    /**
     * Redirect to Google OAuth authentication page.
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws Exception
     */

    public function redirectToGoogle(): \Illuminate\Http\RedirectResponse
    {
        $client = new Client();
        $client->setAuthConfig(storage_path(config('services.google.credentials_path')));
        $client->addScope(config('services.google.scopes'));
        $client->setAccessType('offline');
        $client->setPrompt('consent');

        return redirect()->away($client->createAuthUrl());
    }

    /**
     * Handle Google OAuth callback and store access token.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws Exception
     */
    public function handleGoogleCallback(Request $request): \Illuminate\Http\RedirectResponse
    {
        $client = new Client();
        $client->setAuthConfig(storage_path(config('services.google.credentials_path')));

        try {
            if ($request->has('code')) {
                $accessToken = $client->fetchAccessTokenWithAuthCode($request->input('code'));

                if (isset($accessToken['refresh_token'])) {
                    file_put_contents(storage_path(config('services.google.token_path')), json_encode($accessToken));
                }

                Session::put('google_access_token', $accessToken);

                return redirect()->route('home')->with('success', 'Successfully authenticated with Google');
            }
        } catch (\Exception $e) {
            \Log::error('Google authentication error', ['error' => $e->getMessage()]);
            return redirect()->route('home')->with('error', 'Google authentication failed');
        }

        return redirect()->route('home')->with('error', 'Google authentication failed');
    }
}
