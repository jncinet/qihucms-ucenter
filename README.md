###扩展包安装：

`composer require jncinet/qihucms-ucenter`

###数据库表安装：在网站根目录运行命令

`php artisan migrate`

###一、添加后台菜单：链接地址=>ucenter/site

###二、在文件【/app/Repositories/AccountRepository.php】添加账户变动触发节点：

####1、钻石
>【钻石处理方法里的return true;前添加】
```
\Qihucms\UCenter\Jobs\UpdateAccount::dispatch([
                    'unionid' => isset($account['openid']['wechat']['unionid']) ? $account['openid']['wechat']['unionid'] : '',
                    'user_id' => $user_id,
                    'amount' => $jewel,
                    'info' => __('account.trigger_event.value')[$trigger_event],
                    'type' => 'jewel'
                ]);
```

####2、积分
>【积分处理方法里的return true;前添加】
```
\Qihucms\UCenter\Jobs\UpdateAccount::dispatch([
                    'unionid' => isset($account['openid']['wechat']['unionid']) ? $account['openid']['wechat']['unionid'] : '',
                    'user_id' => $user_id,
                    'amount' => $integral,
                    'info' => __('account.trigger_event.value')[$trigger_event],
                    'type' => 'integral'
                ]);
```  
####3、余额
>【余额处理方法里的return true;前添加】
```
\Qihucms\UCenter\Jobs\UpdateAccount::dispatch([
                    'unionid' => isset($account['openid']['wechat']['unionid']) ? $account['openid']['wechat']['unionid'] : '',
                    'user_id' => $user_id,
                    'amount' => $balance,
                    'info' => __('account.trigger_event.value')[$trigger_event],
                    'type' => 'balance``'
                ]);
```
###三、在文件【app/Http/Controllers/Auth/RegisterController.php】中添加会员同步触发点：

> 在 $user = $this->user->create($data); 后面添加

```
\Qihucms\UCenter\Jobs\UpdateUser::dispatch($user)->delay(now()->addMinutes(1));
```

##四、接口地址

http://api.domain.name/ucenter/{site_id}/user
+ 接收参数：
- string sign 签名 必填
- string mobile 会员手机号码 必填
- string unionid 开放平台ID 可选
- string pmobile 会员上级手机号码 可选
- string punionid 会员上级开放平台ID 可选
- string password 密码 可选
+ 返回值
- string status (success | error) 状态
- number code 提示码
- string message 提示信息

http://api.domain.name/ucenter/{site_id}/account
+ 接收参数：
- string sign 签名 必填
- string type (integral,jewel,balance) 更新类型说明 必填
- string mobile 会员手机号码 二选一
- string unionid 开放平台ID 二选一
- float amount 变化金额 必填正数加，负数减
- string event 更新的触发事件 可选
- string info 更新说明 可选
+ 返回值
- string status (success | error) 状态
- number code 提示码
- string message 提示信息
> {site_id}对应后台添加的关联站点的ID