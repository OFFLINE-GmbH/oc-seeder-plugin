<?php

namespace OFFLINE\Seeder\Models;


use Carbon\Carbon;
use Model;

class Seed extends Model
{
    public $table = 'offline_seeder_seeds';

    public $timestamps = false;
    public $dates = ['seeded_at'];
    public $fillable = ['seeder'];

    public function beforeCreate()
    {
        $this->seeded_at = Carbon::now();
    }
}