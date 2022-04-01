<?php

namespace App\Models;

use App\Models\Concerns\AsJson;
use App\Models\Concerns\SerializeDate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class WechatUser extends Model
{
    use AsJson, SerializeDate;
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * @var string[]
     */
    protected $guarded = ['openid', 'unionid'];
}
