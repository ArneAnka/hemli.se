<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class GuestUpload extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id','original_name','mime','size_bytes','storage_path','iv_base64','expires_at','download_token'
    ];
    protected $casts = [
    'expires_at' => 'datetime',
    'used_at'    => 'datetime',
];


    protected static function booted() {
        static::creating(function ($m) {
            if (!$m->id) $m->id = (string) Str::uuid();
            if (!$m->download_token) $m->download_token = Str::random(48);
        });
    }
}
