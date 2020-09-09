<?php

namespace Qihucms\UCenter\Models;

use Illuminate\Database\Eloquent\Model;

class UcenterSite extends Model
{
    protected $fillable = [
        'name', 'user_api', 'account_api', 'token', 'encrypt_type', 'desc', 'status'
    ];
}
