<?php

namespace Qihucms\UCenter\Jobs;

use App\Models\Spread;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Qihucms\UCenter\Models\UcenterSite;
use Qihucms\UCenter\Support;

class UpdateUser implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;

    /**
     * Create a new job instance.
     *
     * @param User $user
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle()
    {
        $sites = UcenterSite::where('status', 1)->whereNotNull('user_api')->get();
        $parent = Spread::where('user_id', $this->user->id)->first();
        if ($parent) {
            $parent_id = $parent->parent_id;
            if ($parent_id > 0) {
                $parent_unionid = $parent->parent_info['open_id'];
                $parent_unionid = is_array($parent_unionid) && isset($parent_unionid['wechat']['unionid']) ? $parent_unionid['wechat']['unionid'] : '';
                $parent_mobile = $parent->parent_info['mobile'];
            } else {
                $parent_unionid = '';
                $parent_mobile = '';
            }
        } else {
            $parent_id = 0;
            $parent_unionid = '';
            $parent_mobile = '';
        }
        foreach ($sites as $site) {
            Support::requestApi($site->user_api, [
                'id' => $this->user->id,
                'username' => $this->user->username,
                'mobile' => $this->user->mobile,
                'unionid' => isset($this->user->openid['wechtat']['unionid']) ? $this->user->openid['wechtat']['unionid'] : '',
                'email' => $this->user->email,
                'nickname' => $this->user->nickname,

                'pid' => $parent_id,
                'punionid' => $parent_unionid,
                'pmobile' => $parent_mobile,
            ]);
        }
    }
}
