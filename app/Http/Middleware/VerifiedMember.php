<?php

namespace App\Http\Middleware;

use App\Exceptions\MemberNotVerified;
use App\Models\Member;
use Closure;
use Illuminate\Support\Facades\Auth;

class VerifiedMember
{
    public function handle($request, Closure $next)
    {
        if (Auth::guest())
            throw new MemberNotVerified();

        if (Auth::user()->type == Member::TYPE_REGULAR)
            throw new MemberNotVerified();

        return $next($request);
    }
}
