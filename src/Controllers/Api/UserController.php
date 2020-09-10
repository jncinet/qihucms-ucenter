<?php

namespace Qihucms\UCenter\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Spread;
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
            $punionid = User::where('open_id->wechat->unionid', $punionid)->value('id') ?: 0;
        } elseif (!empty($pmobile)) {
            $punionid = User::where('username', $pmobile)->orWhere('mobile', $pmobile)->value('id') ?: 0;
        } else {
            $punionid = 0;
        }

        // 更新或创建会员数据
        if (!empty($unionid)) {
            $user = User::where('open_id->wechat->unionid', $unionid)->first();
            if ($user) {
                $user->username = empty($user->username) ? $mobile : $user->username;
                $user->mobile = empty($user->mobile) ? $mobile : $user->mobile;
                $user->save();
            } else {
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
            // 当前用户关系
            $userSpread = Spread::where('user_id', $user->id)->first();
            // 当前用户上级关系
            $parentUserSpread = Spread::where('user_id', $punionid)->first();
            // 如果上级不存在
            if (!$parentUserSpread) {
                $parentUserSpread = $spreadRepository->create($punionid);
            }

            if ($userSpread) {
                // 如果当前没有上下级关系
                if ($userSpread->parent_id == 0) {
                    $userSpread->parent_id = $punionid;
                    $userSpread->grandfather_id = $parentUserSpread->parent_id;
                    $userSpread->save();

                    // 更新师傅的徒弟数
                    $spreadRepository->updateSonCount($punionid);

                    // 更新太师的徒孙数
                    if ($parentUserSpread->parent_id > 0) {
                        $spreadRepository->updateGrandsonCount($parentUserSpread->parent_id);
                    }
                }
            } else {
                $spreadRepository->create($user->id, $punionid);
            }
        }

        return response()->json([
            'status' => 'success',
            'code' => 1,
            'message' => 'Update succeeded.'
        ]);
    }
}
