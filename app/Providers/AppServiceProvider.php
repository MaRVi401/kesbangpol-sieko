<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Vite;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Vite::useCspNonce(request()->attributes->get('csp_nonce'));

        Gate::define('super-admin-only', function (User $user) {
            return $user->role === 'super_admin';
        });

        Gate::define('pemohon', function (User $user) {
            return $user->role === 'pemohon';
        });

        Gate::define('petugas_verifikasi_data', function (User $user) {
            return $user->role === 'petugas_verifikasi_data';
        });

        Gate::define('petugas_verifikasi_lapangan', function (User $user) {
            return $user->role === 'petugas_verifikasi_lapangan';
        });

        Gate::define('analis_kebijakan_ahli_muda', function (User $user) {
            return $user->role === 'analis_kebijakan_ahli_muda';
        });

        Gate::define('kabid_kesbak', function (User $user) {
            return $user->role === 'kabid_kesbak';
        });

        Gate::define('sekban', function (User $user) {
            return $user->role === 'sekban';
        });

        Gate::define('kaban', function (User $user) {
            return $user->role === 'kaban';
        });

        View::composer('partials.dashboard.sidebar', function ($view) {
            $path = resource_path('json/menu.json');
            $menuData = json_decode(file_get_contents($path), true);

            $user = Auth::user();
            $userRole = $user ? $user->role : null;

            $filteredMenu = collect($menuData['menu'])->firstWhere('role', $userRole);

            $view->with('verticalMenu', $filteredMenu['items'] ?? []);
        });
    }
}