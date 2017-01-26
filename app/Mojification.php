<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Mojification extends Model
{
    protected $fillable = ['sender', 'receiver', 'emoji'];
}
