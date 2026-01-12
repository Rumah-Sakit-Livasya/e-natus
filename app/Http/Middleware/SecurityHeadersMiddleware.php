<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeadersMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // HSTS (PCI DSS)
        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');

        // X-Frame-Options
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // X-Content-Type-Options
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // X-XSS-Protection
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Referrer-Policy (GDPR)
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Content-Security-Policy - Compatible with Filament & Laravel
        $csp = implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval'",
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com",
            "font-src 'self' data: https://fonts.gstatic.com",
            "img-src 'self' data: https: blob:",
            "connect-src 'self'",
            "frame-src 'self'",
            "object-src 'none'",
            "base-uri 'self'",
            "form-action 'self'",
            "upgrade-insecure-requests"
        ]);
        $response->headers->set('Content-Security-Policy', $csp);

        // Permissions-Policy - Allow geolocation for attendance features
        $permissions = 'camera=(), microphone=(), geolocation=(self)';
        $response->headers->set('Permissions-Policy', $permissions);

        // Rate Limiting Headers (anti-scraping)
        $response->headers->set('X-RateLimit-Limit', '120');
        $response->headers->set('X-RateLimit-Remaining', '120');
        $response->headers->set('X-RateLimit-Reset', time() + 60);

        // Additional Security Headers
        $response->headers->set('X-Download-Options', 'noopen');
        $response->headers->set('X-Permitted-Cross-Domain-Policies', 'none');
        $response->headers->set('X-DNS-Prefetch-Control', 'off');

        // Bot Protection Headers
        $response->headers->set('X-Robots-Tag', 'noindex, nofollow, noarchive');

        // Ensure all cookies have security flags
        $cookies = $response->headers->getCookies();
        foreach ($cookies as $cookie) {
            $response->headers->setCookie(
                new \Symfony\Component\HttpFoundation\Cookie(
                    $cookie->getName(),
                    $cookie->getValue(),
                    $cookie->getExpiresTime(),
                    $cookie->getPath(),
                    $cookie->getDomain(),
                    true,  // Secure
                    true,  // HttpOnly
                    false, // Raw
                    'strict' // SameSite
                )
            );
        }

        return $response;
    }
}
