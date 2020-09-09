安装：

composer require jncinet/qihucms-ucenter

添加数据库：
php artisan migrate

一、添加后台菜单：链接地址=>ucenter/site

二、在文件【/app/Repositories/AccountRepository.php】添加账户变动触发节点：

1、钻石【钻石处理方法里的return true;前添加】

\Qihucms\UCenter\Jobs\UpdateAccount::dispatch([
                    'unionid' => isset($account['openid']['wechat']['unionid']) ? $account['openid']['wechat']['unionid'] : '',
                    'user_id' => $user_id,
                    'amount' => $jewel,
                    'info' => __('account.trigger_event.value')[$trigger_event],
                    'type' => 'jewel'
                ]);


2、积分【积分处理方法里的return true;前添加】

\Qihucms\UCenter\Jobs\UpdateAccount::dispatch([
                    'unionid' => isset($account['openid']['wechat']['unionid']) ? $account['openid']['wechat']['unionid'] : '',
                    'user_id' => $user_id,
                    'amount' => $integral,
                    'info' => __('account.trigger_event.value')[$trigger_event],
                    'type' => 'integral'
                ]);
                
3、余额【余额处理方法里的return true;前添加】

\Qihucms\UCenter\Jobs\UpdateAccount::dispatch([
                    'unionid' => isset($account['openid']['wechat']['unionid']) ? $account['openid']['wechat']['unionid'] : '',
                    'user_id' => $user_id,
                    'amount' => $balance,
                    'info' => __('account.trigger_event.value')[$trigger_event],
                    'type' => 'balance``'
                ]);

三、在文件【app/Http/Controllers/Auth/RegisterController.php】中添加会员同步触发点：

// 在 $user = $this->user->create($data); 后面添加

\Qihucms\UCenter\Jobs\UpdateUser::dispatch($user)->delay(now()->addMinutes(1));
