<?php

namespace App\Micro;

use Exception;
use Carbon\Carbon;
use App\Models\Member;
use App\Services\Firebase;
use Illuminate\Contracts\Auth\Guard;
use App\Models\EPaperSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthEndpoint extends Controller
{
    const REGULAR_MEMBER = 'regular';
    const SOCIAL_MEMBER = 'social';

    public function profile()
    {
        $user = $this->_getProfile(Auth::user());

        return $this->res($user, true);
    }

    public function profilePost(Request $request)
    {
        $rules = [
            'name' => 'required',
            'phone' => 'required|phone',
            'address' => 'required',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,bmp|max:2048',
        ];

        $this->validate($request, $rules);

        $req = $request->only(['name', 'phone', 'address']);
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');

            if (!$file->isValid())
                return $this->res('Image not valid');

            try {
                $fileimage = $file->store('member_avatar');
            } catch (Exception $e) {
                report($e);
                return $this->res('Avatar not uploaded: ' . $e->getMessage());
            }
            $req['avatar'] = $fileimage;
        }

        $user = Auth::user();
        foreach ($req as $k => $r)
            $user->{$k} = $r;
        $user->save();

        $user = $this->_getProfile($user);

        return $this->res($user, true);
    }

    public function change_password(Request $request)
    {
        $rules = [
            'old_password' => 'nullable',
            'password' => 'required|confirmed|min:6',
        ];
        $this->validate($request, $rules);

        $req = $request->all();
        $user = Auth::user();

        if (! is_null($user->password) && is_null($request->get('old_password', null))) {
            return $this->res("Old password is required");
        }

        if (is_null($user->password)) {
            $user->password = bcrypt($req['password']);
            $user->save();
        } else {
            $old_pass = $req['old_password'];
            $check = Hash::check($old_pass, $user->password);
            if (!$check)
                return $this->res("Old password does not match");

            $user->password = bcrypt($req['password']);
            $user->save();
        }

        $message = 'Password changed';

        return $this->res($message, true);
    }

    public function register(Request $request, Guard $auth)
    {
        $rules = [
            'name' => 'required',
            'email' => 'required',
            'password' => 'nullable|min:6',
            'oauth_token' => 'nullable',
            'via' => 'required',
        ];
        $this->validate($request, $rules);

        $req = $request->all();

        if ($request->has('password') && $req['password'])
            $req['password'] = bcrypt($req['password']);
        else unset($req['password']);

        if (!($request->has('oauth_token') && $req['oauth_token']))
            unset($req['oauth_token']);

        $channel = self::REGULAR_MEMBER;
        if (isset($req['oauth_token']))
            $channel = self::SOCIAL_MEMBER;

        $user = null;
        $existing_user = Member::where('email', $req['email'])->first();
        if ($existing_user) { // user already exist
            if ($channel == self::SOCIAL_MEMBER) {
                if (! is_null($existing_user->oauth_token) && $existing_user->oauth_token != $req['oauth_token']) {
                    return $this->res('Invalid social user token');
                }
            }
            if ($channel == self::REGULAR_MEMBER) {
                return $this->res('User already registered');
            }
            $user = $existing_user;
        } else { // user not exist yet
            $meta = [
                'channel' => $channel,
                'via' => $req['via'],
                'req' => $request->all(),
            ];

            if ($channel == self::SOCIAL_MEMBER) {
                list($success, $data_social) = Firebase::authUser($req['oauth_token']);
                if (!$success)
                    return $this->res($data_social);

                $meta['social'] = $data_social;
            }

            $req['metadata'] = $meta;
            $user = Member::create($req);
        }

        $auth->login($user);
        $token = $auth->issue();

        $result = [
            'token' => $token,
            'user' => $this->_getProfile($user),
        ];

        return $this->res($result, true);
    }

    public function login(Request $request, Guard $auth)
    {
        $rules = [
            'email' => 'required',
            'password' => 'required|min:6',
            'via' => 'required',
        ];
        $this->validate($request, $rules);

        $req = $request->all();

        $user = Member::where('email', $req['email'])->first();
        if (!$user)
            return $this->res('User not found');

        if (is_null($user->password))
            return $this->res('Password not setup for current user, use Social login');

        $check = Hash::check($req['password'], $user->password);
        if (!$check)
            return $this->res('Password does not match');

        //todo handling log login
        $meta = [
            'channel' => self::REGULAR_MEMBER,
            'via' => $req['via'],
            'req' => $request->all(),
        ];
        $user->metadata = $meta;

        $auth->login($user);
        $token = $auth->issue();

        $result = [
            'token' => $token,
            'user' => $this->_getProfile($user),
        ];

        return $this->res($result, true);
    }

    public function refresh_jwt(Guard $auth)
    {
        $new_token = $auth->refresh();

        return $this->res($new_token, true);
    }

    private function _getProfile(Member $user)
    {
        $today = Carbon::now()->format('Y-m-d');

        if ($user->avatar == 'nopic.png') $user->avatar = 'member_avatar/nouser.png';

        $is_subs = (bool) EPaperSubscription::query()
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->where('member_id', $user->getKey())
            ->count();

        return [
            'name' => $user->name,
            'email' => $user->email,
            'avatar' => generate_cdn($user->avatar),
            'phone' => (string)$user->phone,
            'address' => (string)$user->address,
            'type' => $user->type,
            'is_password_set' => ! is_null($user->password),
            'is_epaper_subs' => $is_subs,
        ];
    }
}
