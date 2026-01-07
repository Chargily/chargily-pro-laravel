<?php

namespace Chargily\ChargilyProLaravel\Http\Middlewares;

use Chargily\ChargilyPro\Auth\Credentials;
use Chargily\ChargilyPro\ChargilyPro;
use Chargily\ChargilyPro\Elements\WebhookElement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Closure;
use Exception;
use Illuminate\Http\JsonResponse;

class ValidateWebhookMiddleware
{
    /**
     * Handle middleware
     *
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $credentials = config('chargily-pro.credentials');
        if ($credentials) {
            try {
                $chargily = new ChargilyPro(new Credentials($credentials));

                $webhook = $chargily->topup()->webhook();

                $data = $webhook->capture();

                if ($data and $data instanceof WebhookElement) {
                    // ===========================
                    // Webhook request is valid ==
                    // Go to the next step      ==
                    // ===========================
                    return $next($request);
                }
            } catch (Exception $e) {
                // laravel.log
                Log::error($e);
            }
        }

        if ($request->expectsJson()) {
            return new JsonResponse([
                'status' => false,
                'message' => 'Invalid webhook request',
            ], 403);
        }

        return abort(403);
    }
}
