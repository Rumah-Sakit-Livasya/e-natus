<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
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

        // ============================================
        // CORE SECURITY HEADERS
        // ============================================

        // HSTS (PCI DSS Compliance) - 2 years with preload
        $response->headers->set('Strict-Transport-Security', 'max-age=63072000; includeSubDomains; preload');

        // X-Frame-Options - Prevent clickjacking
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // X-Content-Type-Options - Prevent MIME sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // X-XSS-Protection - Legacy XSS protection
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Referrer-Policy (GDPR Compliance)
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // ============================================
        // CONTENT SECURITY POLICY (ENHANCED)
        // ============================================

        $isAdminPath = $request->is('admin*') || $request->is('dashboard*') || $request->is('filament*');

        if ($isAdminPath) {
            // Relaxed CSP for admin panel (Filament compatibility)
            $csp = implode('; ', [
                "default-src 'self'",
                "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net",
                "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net https://fonts.bunny.net",
                "font-src 'self' data: https://fonts.gstatic.com https://fonts.bunny.net",
                "img-src 'self' data: https: blob:",
                "connect-src 'self'",
                "frame-src 'self'",
                "object-src 'none'",
                "base-uri 'self'",
                "form-action 'self'",
                "frame-ancestors 'self'",
                "block-all-mixed-content"
            ]);
        } else {
            // Strict CSP for public pages
            $csp = implode('; ', [
                "default-src 'self'",
                "script-src 'self' https://cdn.jsdelivr.net",
                "style-src 'self' https://fonts.googleapis.com https://cdn.jsdelivr.net",
                "font-src 'self' data: https://fonts.gstatic.com",
                "img-src 'self' data: https:",
                "connect-src 'self'",
                "frame-src 'none'",
                "object-src 'none'",
                "base-uri 'self'",
                "form-action 'self'",
                "frame-ancestors 'none'",
                "upgrade-insecure-requests",
                "block-all-mixed-content"
            ]);
        }

        $response->headers->set('Content-Security-Policy', $csp);

        // ============================================
        // PERMISSIONS POLICY (ENHANCED)
        // ============================================

        $permissions = implode(', ', [
            'camera=()',
            'microphone=()',
            'geolocation=(self)',
            'payment=()',
            'usb=()',
            'magnetometer=()',
            'gyroscope=()',
            'accelerometer=()',
            'autoplay=()',
            'encrypted-media=()',
            'fullscreen=(self)',
            'picture-in-picture=()'
        ]);
        $response->headers->set('Permissions-Policy', $permissions);

        // ============================================
        // ADVANCED RATE LIMITING HEADERS
        // ============================================

        $clientIp = $request->ip();
        $cacheKey = 'rate_limit:' . $clientIp;

        // Get current request count
        $requests = Cache::get($cacheKey, 0);
        $maxAttempts = 120;
        $decayMinutes = 1;
        $resetTime = time() + ($decayMinutes * 60);

        // Increment counter
        Cache::put($cacheKey, $requests + 1, now()->addMinutes($decayMinutes));

        // Set rate limit headers
        $response->headers->set('X-RateLimit-Limit', (string)$maxAttempts);
        $response->headers->set('X-RateLimit-Remaining', (string)max(0, $maxAttempts - $requests - 1));
        $response->headers->set('X-RateLimit-Reset', (string)$resetTime);

        // Add Retry-After header if limit exceeded
        if ($requests >= $maxAttempts) {
            $response->headers->set('Retry-After', (string)($decayMinutes * 60));
        }

        // ============================================
        // ADDITIONAL SECURITY HEADERS
        // ============================================

        $response->headers->set('X-Download-Options', 'noopen');
        $response->headers->set('X-Permitted-Cross-Domain-Policies', 'none');
        $response->headers->set('X-DNS-Prefetch-Control', 'off');

        // Expect-CT header for Certificate Transparency
        $response->headers->set('Expect-CT', 'max-age=86400, enforce');

        // ============================================
        // ADVANCED BOT PROTECTION
        // ============================================

        $userAgent = $request->userAgent() ?? '';
        $botScore = $this->calculateBotScore($request, $userAgent);

        // Block high-risk bots
        if ($botScore >= 8) {
            Log::warning('High-risk bot blocked', [
                'ip' => $clientIp,
                'user_agent' => $userAgent,
                'path' => $request->path(),
                'score' => $botScore
            ]);

            // Create 403 response with all security headers
            $blockedResponse = response('Access Denied', 403);
            $blockedResponse->headers->set('Strict-Transport-Security', 'max-age=63072000; includeSubDomains; preload');
            $blockedResponse->headers->set('X-Frame-Options', 'SAMEORIGIN');
            $blockedResponse->headers->set('X-Content-Type-Options', 'nosniff');
            $blockedResponse->headers->set('X-XSS-Protection', '1; mode=block');
            $blockedResponse->headers->set('X-Robots-Tag', 'noindex, nofollow, noarchive, nosnippet');
            $blockedResponse->headers->set('Cache-Control', 'no-store');
            $blockedResponse->headers->set('Content-Security-Policy', "default-src 'none'");

            return $blockedResponse;
        }

        // Tag suspicious bots
        if ($botScore >= 5) {
            $response->headers->set('X-Robots-Tag', 'noindex, nofollow, noarchive, nosnippet');
        }

        // Protect sensitive paths
        if ($request->is('admin/*') || $request->is('api/*') || $request->is('login')) {
            $response->headers->set('X-Robots-Tag', 'noindex, nofollow, noarchive');
            $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, private');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', '0');
        }

        // ============================================
        // SECURE COOKIE CONFIGURATION
        // ============================================

        $cookies = $response->headers->getCookies();
        foreach ($cookies as $cookie) {
            $response->headers->setCookie(
                new \Symfony\Component\HttpFoundation\Cookie(
                    $cookie->getName(),
                    $cookie->getValue(),
                    $cookie->getExpiresTime(),
                    $cookie->getPath(),
                    $cookie->getDomain(),
                    true,  // Secure - HTTPS only
                    true,  // HttpOnly - No JavaScript access
                    false, // Raw
                    'strict' // SameSite - CSRF protection
                )
            );
        }

        return $response;
    }

    /**
     * Calculate bot risk score (0-10, higher = more suspicious)
     *
     * @param Request $request
     * @param string $userAgent
     * @return int
     */
    private function calculateBotScore(Request $request, string $userAgent): int
    {
        $score = 0;

        // Known malicious bots (+8)
        $maliciousBots = [
            'AhrefsBot',
            'SemrushBot',
            'DotBot',
            'MJ12bot',
            'BLEXBot',
            'PetalBot',
            'Bytespider',
            'DataForSeoBot',
            'serpstatbot'
        ];
        foreach ($maliciousBots as $bot) {
            if (stripos($userAgent, $bot) !== false) {
                $score += 8;
                break;
            }
        }

        // Scraping tools (+7)
        $scrapingTools = [
            'scrapy',
            'curl',
            'wget',
            'python-requests',
            'go-http-client',
            'axios',
            'node-fetch',
            'httpie',
            'postman'
        ];
        foreach ($scrapingTools as $tool) {
            if (stripos($userAgent, $tool) !== false) {
                $score += 7;
                break;
            }
        }

        // Empty user agent (+5)
        if (empty($userAgent)) {
            $score += 5;
        }

        // Missing common headers (+2 each)
        if (!$request->header('Accept-Language')) {
            $score += 2;
        }
        if (!$request->header('Accept-Encoding')) {
            $score += 2;
        }

        // Suspicious request patterns (+3)
        if ($request->method() === 'HEAD' && !$request->is('up')) {
            $score += 3;
        }

        // High request rate from same IP (+2)
        $clientIp = $request->ip();
        $requestCount = Cache::get('rate_limit:' . $clientIp, 0);
        if ($requestCount > 100) {
            $score += 2;
        }

        return min($score, 10);
    }

    /**
     * Detect suspicious bot user agents (legacy method, kept for compatibility)
     *
     * @param string $userAgent
     * @return bool
     */
    private function isSuspiciousBot(string $userAgent): bool
    {
        return $this->calculateBotScore(request(), $userAgent) >= 5;
    }
}
