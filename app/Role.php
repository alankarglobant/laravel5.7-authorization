<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'roles';
    const ROLE_VENDOR = 1;
    const ROLE_USER = 2;
    const ROLE_STAFF = 3;
}
