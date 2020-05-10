<?php

namespace App\Http\Middleware;

use App\Jobs\VisitorEnter;
use Illuminate\Routing\Middleware\ThrottleRequests as ThrottleBase;
use RuntimeException;

class ThrottleRequests extends ThrottleBase {

    protected function resolveRequestSignature($request)
    {
        if (! $request->route()) {
            throw new RuntimeException('Unable to generate fingerprint. Route unavailable.');
        }

        $methods = $request->route()->methods();
        $domain = $request->route()->domain();
        $uri = $request->route()->uri();
        $ip = $request->header('ip') ?? $request->ip();
        $uid = $request->header('X-uid', '0');
        $user_agent = $request->header('user-agent');
        $client = $request->header('client') ?? 'no-key';
        $user_id = 0;
        $user = $request->user();
        if ($user) $user_id = $user->getKey();
        $parameter = $_REQUEST;

        $payload = compact('methods', 'domain', 'client', 'uri', 'user_id', 'ip', 'uid', 'user_agent', 'parameter');
        VisitorEnter::dispatch($payload);

        $builder = sprintf("%s|%s|%s|%s|%s", implode('|', $methods), $domain, $uri, $ip, $uid);

        return sha1($builder);
    }

}
