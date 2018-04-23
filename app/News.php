<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    protected $fillable = ['title','content','status'];


    public function topics()
    {
      return $this->belongsToMany(Topic::class);

    }
}
