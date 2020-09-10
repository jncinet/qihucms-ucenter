<?php

namespace Qihucms\UCenter\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Repositories\AccountRepository;
use Illuminate\Http\Request;
use Qihucms\UCenter\Support;

class AccountController extends Controller
{
    public function update(Request $request, $id)
    {
        if (!Support::VerifySign($id)) {
            return response()->json([
                'status' => 'error',
                'code' => 0,
                'message' => 'Sign verify failed.'
            ]);
        }

        $unionid = $request->input('unionid');
        $mobile = $request->input('mobile');
        if (empty($unionid) && empty($mobile)) {
            return response()->json([
                'status' => 'error',
                'code' => 0,
                'message' => 'User does not exist.'
            ]);
        } else {
            if ($unionid) {
                $user = User::where('wechat->unionid', $unionid)->first();
            } elseif ($mobile) {
                $user = User::where('username', $mobile)->orWhere('mobile', $mobile)->first();
            } else {
                $user = null;
            }
            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'code' => 0,
                    'message' => 'User does not exist.'
                ]);
            }
        }

        $amount = $request->input('amount');
        if ($amount == 0) {
            return response()->json([
                'status' => 'error',
                'code' => 0,
                'message' => 'Amount cannot be 0.'
            ]);
        }

        $info = $request->input('info');
        $event = $request->input('event', 'ucenter_update');

        $account = new AccountRepository();
        $type = $request->input('type');
        switch ($type) {
            case 'integral':
                $result = $account->updateIntegral($user->id, $amount, $event, ['UC_info' => $info]);
                break;
            case 'jewel':
                $result = $account->updateJewel($user->id, $amount, $event, ['UC_info' => $info]);
                break;
            case 'balance':
                $result = $account->updateBalance($user->id, $amount, $event, ['UC_info' => $info]);
                break;
            default:
                return response()->json([
                    'status' => 'error',
                    'code' => 0,
                    'message' => 'Type does not exist.'
                ]);
        }
        if ($result) {
            return response()->json([
                'status' => 'success',
                'code' => 1,
                'message' => 'Update succeeded.'
            ]);
        }
        return response()->json([
            'status' => 'error',
            'code' => 0,
            'message' => 'Update failed.'
        ]);
    }
}
