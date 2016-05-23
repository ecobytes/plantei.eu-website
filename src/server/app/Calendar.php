<?php

namespace Caravel;

use Illuminate\Database\Eloquent\Model;

class Calendar extends Model
{
    protected $fillable = [
        'category', 'location', 'start', 'end', 'title',
        'description', 'address', 'user_id' //, 'image'
    ];

    protected $table = 'calendar';

    /**
     * Get sementecas associated with event
     *
     * @return BelongsToMany
     */
    public function sementecas()
    {
        return $this->belongsToMany('Caravel\Sementeca', 'sementecas_calendar');
    }

    /**
     * Get calendar events in request interval
     *
     * @return Collection
     */
    public static function interval($request)
    {
        return Calendar::where('start', '>=', $request->input('start'))
            ->where('end', '<', $request->input('end'));
    }

}
