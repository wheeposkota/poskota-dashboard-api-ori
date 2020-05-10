<?php

namespace App\Micro;

use App\Models\FirebaseTokenDevice;
use App\Services\Firebase;
use App\Services\Generic;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Request;
use Illuminate\Http\Request as BaseRequest;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function home()
    {
        $res = date('Y-m-d H:i:s');
        return $this->res($res, true);
    }

    public function version()
    {
        $res = Generic::getver();
        return $this->res($res, true);
    }

    public function subsTopic(BaseRequest $request)
    {
        $rules = [
            'registration_token' => 'required',
        ];
        $this->validate($request, $rules);

        $token = $request->get('registration_token');
        $topic = Firebase::TOPIC_NEWS;

        $device = Firebase::subsMessaging($token, $topic);

        FirebaseTokenDevice::create([
            'member_id' => Auth::id() ?? 0,
            'registration_token' => $token,
            'topic' => $topic,
            'metadata' => $device,
        ]);

        return $this->res($token, true);
    }

    public function cacheFlush()
    {
        Cache::flush();
        return $this->res(':D', true);
    }

    public function assetStatic($file)
    {
        $filename = mt_rand(1000, 9999) . '.jpg'; //todo
        $headers = [
            "Content-Disposition" => "inline; filename=\"$filename\""
        ];

        if (!\Storage::exists($file)) {
            return \Response::download(base_path('nopic.png'), $filename, $headers);
        }

        return \Storage::download($file, $filename, $headers);
    }

    public function callFcm()
    {
        if (Request::method() == 'POST') :

            $title = Request::get('title');
            $body = Request::get('body');

            Firebase::sendMessaging($title, $body);

            return "fcm sent, title: {$title}, body: {$body}";
        endif;

        $drop = [
            'type' => '1',
        ];

        $csrf = csrf_field();
        $list = "";
        // foreach ($drop as $key => $value) {
        //     $list .= "<option value='$key'>$value</option>";
        // }

        return <<<HTML
            <form method="post">
                $csrf
                <!-- <select name="type"> -->
                    <!-- $list -->
                <!-- </select> -->
                <input type="text" name="title" placeholder="title">
                <input type="text" name="body" placeholder="body">
                <input type="submit" value="Send Fcm!">
            </form>
HTML;
    }

    protected function res($data = [], $success = false)
    {
        $response = [
            'success' => $success,
            'code' => 400,
            'message' => "",
            'data' => null,
        ];

        if ($success) {
            $response['code'] = 200;
            $response['data'] = $data;
        } else {
            $response['message'] = $data;
        }

        return response()->json($response);
    }

    protected function user()
    {
        return Auth::user();
    }

    protected function id()
    {
        return $this->user()->id ?? 0;
    }

}
