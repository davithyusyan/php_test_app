<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Laravel\Passport\Client;

class AuthController
{
    public function login(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'login' => 'required|min:4|max:180',
                'password' => 'required|min:6|max:180',
            ]);

            $user = User::where('login', $request->get('login'));
            if (!$user->exists()) {
                return response()->json([
                    'message' => 'Ошибка авторизации (Неверный логин)',
                    'error' => true,
                ], 422);
            }

            $user = $user->first();

            if (!Hash::check($request->get('password'), $user->password, [])) {
                return response()->json([
                    'message' => 'Ошибка авторизации (Неверный пароль)',
                    'error' => true,
                ], 422);
            }

            return $this->getToken($request, $user, 'password');
        } catch (\Exception $ex) {
            return response()->json([
                'data' => [],
                'message' => "Системная ошибка ({$ex->getMessage()})",
                'error' => true,
            ]);
        }
    }

    public function refresh(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'refresh_token' => 'required',
            ]);

            return $this->getToken($request, auth()->user(), 'refresh_token');
        } catch (\Exception $ex) {
            return response()->json([
                'data' => [],
                'message' => "Системная ошибка ({$ex->getMessage()})",
                'error' => true,
            ]);
        }
    }

    public function logout(Request $request): JsonResponse
    {
        try {
            $token = $request->user()->token();
            $token->revoke();
            return response()->json([
                'data' => [],
                'message' => 'Вы успешно вышли из системы',
                'error' => false,
            ]);
        } catch (\Exception $ex) {
            return response()->json([
                'data' => [],
                'message' => "Системная ошибка ({$ex->getMessage()})",
                'error' => true,
            ]);
        }
    }

    private function getToken(Request $request, $user, string $grantType): JsonResponse
    {
        $client = Client::where('password_client', 1)->first();
        if (!$client) {
            return response()->json([
                'message' => 'Ошибка авторизации (КОД-1)',
                'error' => true,
            ], 400);
        }

        $proxy = Request::create('oauth/token', 'POST');
        $request->request->add([
            'grant_type' => $grantType,
            'client_id' => $client->id,
            'client_secret' => $client->secret,
            'scope' => '*',
            'username' => $request->get('login'),
            'password' => $request->get('password'),
            'refresh_token' => $request->get('refresh_token'),
        ]);

        $oauthResponse = Route::dispatch($proxy);
        $content = json_decode($oauthResponse->content());

        if ($oauthResponse->status() != 200) {
            $msg = $content->message;
            return response()->json([
                'data' => $content,
                'message' => "Ошибка авторизации ($msg)",
                'error' => true,
            ], 400);
        }

        return response()->json([
            'data' => [
                'token' => $content,
                'user' => $user,
            ],
            'message' => '',
            'error' => false,
        ]);
    }
}
