<?php

namespace App\Http\Controllers\User;

use App\Notifications\EverCodeNotification;
use BeyondCode\QueryDetector\Outputs\Log;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notification;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Mail\EverCodeVerification;




class AuthorizationController extends Controller
{
    protected function checkCodeValidity($user,$addMin = 2)
    {
        if (!$user->ver_code_send_at){
            return false;
        }
        if ($user->ver_code_send_at->addMinutes($addMin) < Carbon::now()) {
            return false;
        }
        return true;
    }

    public function authorizeForm()
    {
        $user = auth()->user();

        if (!$user->status) {
            $pageTitle = 'Banned';
            $type = 'ban';
        } elseif (!$user->ev) {
            $type = 'email';
            $pageTitle = 'Verify Email';
        } elseif (!$user->sv) {
            $type = 'sms';
            $pageTitle = 'Verify Mobile Number';
        } elseif (!$user->tv) {
            $pageTitle = '2FA Verification';
            $type = '2fa';
        } else {
            return to_route('user.home');
        }

        // Check code validity and regenerate if needed
        if (!$this->checkCodeValidity($user) && ($type != '2fa') && ($type != 'ban')) {
            $verificationCode = verificationCode(6);
            $user->ver_code = $verificationCode;
            $user->ver_code_send_at = Carbon::now();
            $user->save();
        
            try {
                // Send the mail
                $result = Mail::to($user->email)->send(new EverCodeVerification($verificationCode));
        
                // Check for failures
                if (count(Mail::failures()) > 0) {
                    $failures = Mail::failures();
                    $notify[] = ['error', 'Error sending verification code. Failed recipients: ' . implode(', ', $failures)];
                    return back()->withNotify($notify);
                }
            } catch (\Exception $exception) {
                $notify[] = ['error', 'Error sending verification code. Please try again.'];
                return back()->withNotify($notify);
            }
        }
        

        return view($this->activeTemplate.'user.auth.authorization.'.$type, compact('user', 'pageTitle'));

    }

    public function sendVerifyCode($type)
    {
        $user = auth()->user();

        if ($this->checkCodeValidity($user)) {
            $targetTime = $user->ver_code_send_at->addMinutes(2)->timestamp;
            $delay = $targetTime - time();
            throw ValidationException::withMessages(['resend' => 'Please try after ' . $delay . ' seconds']);
        }

        $user->ver_code = verificationCode(6);
        $user->ver_code_send_at = Carbon::now();
        $user->save();
    

        try {
            Mail::to($user->email)->send(new EverCodeVerification($user->ver_code));
            dd('Email sent successfully!');

        } catch (\Exception $exception) {
            $notify[] = ['error', 'Error sending verification code. Please try again.'];
            return back()->withNotify($notify);
        }

        $notify[] = ['success', 'Verification code sent successfully'];
        return back()->withNotify($notify);
    }

    public function emailVerification(Request $request)
    {
        $request->validate([
            'code'=>'required'
        ]);

        $user = auth()->user();

        if ($user->ver_code == $request->code) {
            $user->ev = 1;
            $user->ver_code = null;
            $user->ver_code_send_at = null;
            $user->save();
            return to_route('user.home');
        }
        throw ValidationException::withMessages(['code' => 'Verification code didn\'t match!']);
    }

    public function mobileVerification(Request $request)
    {
        $request->validate([
            'code' => 'required',
        ]);


        $user = auth()->user();
        if ($user->ver_code == $request->code) {
            $user->sv = 1;
            $user->ver_code = null;
            $user->ver_code_send_at = null;
            $user->save();
            return to_route('user.home');
        }
        throw ValidationException::withMessages(['code' => 'Verification code didn\'t match!']);
    }

    public function g2faVerification(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'code' => 'required',
        ]);
        $response = verifyG2fa($user,$request->code);
        if ($response) {
            $notify[] = ['success','Verification successful'];
        }else{
            $notify[] = ['error','Wrong verification code'];
        }
        return back()->withNotify($notify);
    }
}