<?php

namespace Qihucms\UCenter;

use GuzzleHttp\Client;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Qihucms\UCenter\Models\UcenterSite;

class Support
{
    /**
     * 验签
     *
     * @param int $id
     * @return bool
     */
    public static function VerifySign($id = 0): bool
    {
        $site = UcenterSite::find($id);
        if ($site) {
            switch ($site->encrypt_type) {
                case 'hash':
                    $data = request()->input();
                    $sign = $data['sign'];
                    unset($data['sign']);
                    return $sign == self::GenerateSign($data, $site->token);
                case 'ip':
                    return request()->ip() === $site->ip;
                default:
                    return request()->input('sign') === $site->token;
            }
        }
        return false;
    }

    /**
     * 生成签名
     *
     * @param array $data
     * @param string $token
     * @return string
     */
    public static function GenerateSign(array $data, $token = ''): string
    {
        $data = Arr::sortRecursive($data);
        return md5(json_encode($data) . $token);
    }

    /**
     * 验证手机号码
     *
     * @param $mobile
     * @return bool
     */
    public static function isMobile($mobile): bool
    {
        if (preg_match("/^1[345789]{1}\d{9}$/", $mobile)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 请求接口
     *
     * @param string $url
     * @param array $data
     * @return Collection
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function requestApi(string $url, array $data): Collection
    {
        $http = new Client(['verify' => false]);

        $response = $http->request('POST', $url, ['json' => $data]);
        $result = json_decode((string)$response->getBody(), true);

        return collect($result);
    }
}