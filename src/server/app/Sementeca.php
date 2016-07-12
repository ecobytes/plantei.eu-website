<?php

namespace Caravel;

use Illuminate\Database\Eloquent\Model;

class Sementeca extends Model
{
    protected $fillable = ['contact', 'description', 'address', 'lat', 'lon', 'name', 'active'];

    public function events() {

        return $this->belongsToMany('Caravel\Calendar', 'sementecas_calendar')
            ->where('end', '>=', \Carbon\Carbon::now());
    }
}
