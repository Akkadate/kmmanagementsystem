<?php

namespace App\Http\Middleware;

use App\Models\ActivityLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($request->user() && in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            $this->logActivity($request);
        }

        return $response;
    }

    private function logActivity(Request $request): void
    {
        $route = $request->route();

        if (!$route) {
            return;
        }

        $action = $this->determineAction($request->method());
        $subject = $this->extractSubject($route);

        if ($subject) {
            ActivityLog::create([
                'user_id' => $request->user()->id,
                'action' => $action,
                'subject_type' => $subject['type'],
                'subject_id' => $subject['id'],
                'properties' => [
                    'url' => $request->fullUrl(),
                    'method' => $request->method(),
                    'ip' => $request->ip(),
                ],
            ]);
        }
    }

    private function determineAction(string $method): string
    {
        return match($method) {
            'POST' => 'created',
            'PUT', 'PATCH' => 'updated',
            'DELETE' => 'deleted',
            default => 'unknown',
        };
    }

    private function extractSubject($route): ?array
    {
        $parameters = $route->parameters();

        foreach (['article', 'category', 'user', 'tag'] as $type) {
            if (isset($parameters[$type])) {
                $model = $parameters[$type];
                return [
                    'type' => get_class($model),
                    'id' => $model->id,
                ];
            }
        }

        return null;
    }
}
