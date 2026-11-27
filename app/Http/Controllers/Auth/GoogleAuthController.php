<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\DefaultDataService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class GoogleAuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback(Request $request)
    {
        if ($request->has('error')) {
            return $this->failure('access_denied');
        }

        try {
            $google = Socialite::driver('google')->user();
        } catch (Throwable $e) {
            report($e);
            return $this->failure('google_failed');
        }

        if (! $google->getEmail()) {
            return $this->failure('no_email');
        }

        $user  = User::where('google_id', $google->getId())->first();
        $isNew = false;

        if (! $user) {
            if (User::where('email', $google->getEmail())->exists()) {
                return $this->failure('email_exists');
            }

            $user = DB::transaction(function () use ($google) {
                $user = new User();
                $user->name = $google->getName() ?: Str::before($google->getEmail(), '@');
                $user->email = $google->getEmail();
                $user->google_id = $google->getId();
                $user->avatar = $google->getAvatar();
                $user->email_verified_at = now();
                $user->save();

                DefaultDataService::createFor($user);

                return $user;
            });

            $isNew = true;
        }

        $token = $user->createToken('google')->plainTextToken;

        return redirect($this->frontendUrl('/auth/callback#' . http_build_query([
            'token'  => $token,
            'is_new' => $isNew ? 1 : 0,
        ])));
    }

    private function failure(string $code)
    {
        return redirect($this->frontendUrl('/auth/callback?error=' . $code));
    }

    private function frontendUrl(string $path): string
    {
        return rtrim(config('services.frontend.url'), '/') . $path;
    }
}