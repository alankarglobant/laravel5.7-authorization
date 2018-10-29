<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{
    protected $table = 'user_roles';
    protected $fillable = ['role_id'];

    public function user()
    {
    	return $this->belongsTo('App\User','user_id');
    }

    public function role()
    {
    	return $this->belongsTo('App\Role','role_id');
    }
}
