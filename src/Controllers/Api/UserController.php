<?php

namespace Qihucms\UCenter\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Repositories\SpreadRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Qihucms\UCenter\Support;

class UserController extends Controller
{
    public function bind(Request $request, SpreadRepository $spreadRepository, $id)
    {
        if (!Support::VerifySign($id)) {
            return response()->json([
                'status' => 'error',
                'code' => 0,
                'message' => 'Sign verify failed.'
            ]);
        }

        $unionid = $request->input('unionid');
        $punionid = $request->input('punionid');
        $mobile = $request->input('mobile');
        $pmobile = $request->input('pmobile');
        $password = $request->input('password');

        // 手机号码
        if (empty($mobile) || !Support::isMobile($mobile)) {
            return response()->json([
                'status' => 'error',
                'code' => 0,
                'message' => 'The username must be a valid mobile phone number.'
            ]);
        }
        // 会员上级
        if (!empty($punionid)) {
            $punionid = User::where('wechat->unionid', $punionid)->value('id') ?: 0;
        } elseif (!empty($pmobile)) {
            $punionid = User::where('username', $pmobile)->orWhere('mobile', $pmobile)->value('id') ?: 0;
        } else {
            $punionid = 0;
        }

        $isCreate = false;
        // 更新或创建会员数据
        if (!empty($unionid)) {
            $user = User::where('wechat->unionid', $unionid)->first();
            if ($user) {
                $user->username = empty($user->username) ? $mobile : $user->username;
                $user->mobile = empty($user->mobile) ? $mobile : $user->mobile;
                $user->save();
            } else {
                $isCreate = true;
                $user = User::create([
                    'open_id' => ['wechat' => ['unionid' => $unionid]],
                    'username' => $mobile,
                    'mobile' => $mobile,
                    'password' => $password ?: Str::random(),
                ]);
            }
        } else {
            $user = User::where('username', $mobile)->orWhere('mobile', $mobile)->first();
            if ($user) {
                $user->mobile = empty($user->mobile) ? $mobile : $user->mobile;
                $user->username = empty($user->username) ? $mobile : $user->username;
                $user->save();
            } else {
                $isCreate = true;
                $user = User::create([
                    'open_id' => ['wechat' => ['unionid' => $unionid]],
                    'username' => $mobile,
                    'mobile' => $mobile,
                    'password' => $password ?: Str::random(),
                ]);
            }
        }

        // 推荐关系
        if ($punionid) {
            if ($isCreate) {
                $spreadRepository->create($user->id, $punionid);
            } else {
                $parent = $spreadRepository->getUserParent($user->id);
                if (!$parent || $parent->parent_id == 0) {
                    $spreadRepository->create($user->id, $punionid);
                }
            }
        }

        return response()->json([
            'status' => 'success',
            'code' => 1,
            'message' => 'Update succeeded.'
        ]);
    }
}
