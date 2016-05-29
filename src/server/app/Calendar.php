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
    //protected $dateFormat = 'Y';

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

    /**
     * Get calendar events happening now
     *
     * @return Collection
     */
    public static function now()
    {
      $now = \Carbon\Carbon::now();
      return Calendar::where('start', '<=', $now)
            ->where('end', '>', $now);
    }

    /**
     * Get calendar events next 7 days
     *
     * @return Collection
     */
    public static function nextDays()
    {
      return Calendar::whereBetween('start', [\Carbon\Carbon::now(), \Carbon\Carbon::now()->addDays(8)])
        ->orderBy('start');
    }

}
