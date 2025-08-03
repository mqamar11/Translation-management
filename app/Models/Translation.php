<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Translation extends Model
{
      protected $fillable = ['key', 'locale', 'value'];

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }
}
