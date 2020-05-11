<?php

namespace Modules\ModuleBase\Entites;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    const TABLE = null;

    public $timestamps = false;
}
