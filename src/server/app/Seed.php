<?php

namespace Caravel;

use Illuminate\Database\Eloquent\Model;

class Seed extends Model
{
  protected $fillable = [
    'local', 'year', 'origin', 'available', 'public', 'description', 'user_id',
    'common_name', 'latin_name', 'species_id', 'variety_id', 'family_id',
    'polinization', 'direct'
  ];
  protected $casts = [
    'public' => 'boolean',
    'available' => 'boolean',
    'user_id' => 'integer'
  ];
  //
  public function months()
  {
    return $this->hasMany('Caravel\SeedMonth');
  }
  public function syncMonths($months_new)
  {
    //TODO: create a dedicated function in \Caravel\Seed->syncMonths
    if ((! $months_new) && ($this->months->count())){
      $this->months()->delete();
    } else {
      $this->months()->whereNotIn('month', $months_new)->delete();
      $months = $this->months->lists('month')->toArray();
      foreach($months_new as $month){
        if (! in_array($month, $months)){
          $this->months()->save(new SeedMonth(['month'=> $month ]));
        }
      }
    }
  }
  public function pictures()
  {
    return $this->hasMany('Caravel\Picture');
  }
  public function cookings()
  {
    return $this->hasMany('Caravel\SeedCooking');
  }
  public function medicines()
  {
    return $this->hasMany('Caravel\SeedMedicine');
  }
  public function variety()
  {
    return $this->belongsTo('Caravel\Variety');
  }
  public function species()
  {
    return $this->belongsTo('Caravel\Species');
  }
  public function family()
  {
    return $this->belongsTo('Caravel\Family');
  }
  public function transactions()
  {
    return $this->hasMany('\Caravel\SeedsExchange', 'seed_id');
  }

}

class Family extends Model
{
  protected $table = 'family';
  protected $fillable = ['name'];
  public function species()
  {
    return $this->hasMany('Caravel\Species');
  }
  public function seeds()
  {
    return $this->hasMany('Caravel\Seed');
  }
}

class Species extends Model
{
  protected $table = 'species';
  protected $fillable = ['name'];
  public function family()
  {
    return $this->belongsTo('Caravel\Family');
  }
  public function varieties()
  {
    return $this->hasMany('Caravel\Variety');
  }
  public function seeds()
  {
    return $this->hasMany('Caravel\Seed');
  }
}

class Variety extends Model
{
  protected $table = 'variety';
  protected $fillable = ['name'];
  public function species()
  {
    return $this->belongsTo('Caravel\Species');
  }
  public function seeds()
  {
    return $this->hasMany('Caravel\Seed');
  }
}


class SeedMonth extends Model
{
  protected $primaryKey = null;
  public $incrementing = false;
  protected $table = 'seeds_months';
  protected $fillable = ['month'];
  public $timestamps = false;
}

class SeedCooking extends Model
{
  protected $fillable = ['recipe'];
  //
  protected $touches = ['seed'];
  protected $table = 'seeds_cooking';
  public function seed()
  {
    return $this->belongsTo('Caravel\Seed');
  }
}

class SeedMedicine extends Model
{
  protected $fillable = ['use'];
  //
  protected $touches = ['seed'];
  protected $table = 'seeds_medicine';
  public function seed()
  {
    return $this->belongsTo('Caravel\Seed');
  }
}

class Picture extends Model
{
  protected $fillable = ['label', 'path', 'md5sum', 'url'];
  protected $touches = ['seed'];
  protected $table = 'pictures';
  public function seed()
  {
    return $this->belongsTo('Caravel\Seed');
  }
}
