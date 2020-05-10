<?php

namespace App\Http\Middleware;

use App\Exceptions\MemberNotSubs;
use App\Models\EPaperSubscription;
use Carbon\Carbon;
use Closure;
use Illuminate\Support\Facades\Auth;

class EpaperSubs
{
    public function handle($request, Closure $next)
    {
        if (Auth::guest())
            throw new MemberNotSubs();

        $today = Carbon::now()->format('Y-m-d');
        $check_subs = EPaperSubscription::query()
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->where('member_id', Auth::id())
            ->first();
        if (!$check_subs)
            throw new MemberNotSubs();

        return $next($request);
    }
}
