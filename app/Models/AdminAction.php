<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminAction extends Model
{
    protected $fillable = ['admin_id', 'action', 'target_type', 'target_id', 'meta'];

    protected $casts = ['meta' => 'array'];

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
}
