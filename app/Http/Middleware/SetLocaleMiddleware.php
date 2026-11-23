<?php

namespace App\Http\Middleware;

use App\Models\SystemSetting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleMiddleware
{
    /**
     * Handle an incoming request and set application locale.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Explicit query parameter ?lang=
        if ($request->has('lang') && in_array($request->query('lang'), ['id', 'en'])) {
            $locale = $request->query('lang');
            Session::put('user_selected_locale', $locale);
            Session::put('app_locale', $locale);
        } elseif (Session::has('user_selected_locale') && in_array(Session::get('user_selected_locale'), ['id', 'en'])) {
            // 2. Explicit user session toggle
            $locale = Session::get('user_selected_locale');
            Session::put('app_locale', $locale);
        } else {
            // 3. Follow SystemSetting (configured in Admin Settings)
            try {
                $locale = SystemSetting::get('app_language', 'id');
            } catch (\Throwable $e) {
                $locale = 'id';
            }
            Session::put('app_locale', $locale);
        }

        if (!in_array($locale, ['id', 'en'])) {
            $locale = 'id';
        }

        App::setLocale($locale);

        return $next($request);
    }
}
