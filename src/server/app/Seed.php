<?php

namespace Caravel;

use Illuminate\Database\Eloquent\Model;

class Seed extends Model
{
  protected $fillable = [
    'local', 'year', 'origin', 'available', 'public', 'description', 'user_id',
    'common_name', 'latin_name', 'species_id', 'variety_id', 'family_id',
    'polinization', 'direct', 'untilharvest', 'units', 'quantity', 'risk', 'traditional',
    'direct'
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
    // return $this->hasMany('Caravel\Picture');
    return $this->morphMany('Caravel\Picture', 'imageable');

  }
  public function uses()
  {
    return $this->morphMany('Caravel\PlantUsage', 'plantuseable');
  }
  public function popnames()
  {
    return $this->morphMany('Caravel\Popname', 'popnameable');
  }
/*public function cookings()
  {
    return $this->hasMany('Caravel\SeedCooking');
  }
  public function medicines()
  {
    return $this->hasMany('Caravel\SeedMedicine');
  }*/
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
  protected $visible = ['name'];
  protected $hidden = ['updated_at', 'created_at'];

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

class Picture extends Model
{
  protected $fillable = ['label', 'path', 'md5sum', 'url'];
  // protected $touches = ['seed'];
  protected $table = 'pictures';
  protected $hidden = ['imageable_id', 'imageable_type', 'created_at', 'updated_at'];

  public function seed()
  {
    // return $this->belongsTo('Caravel\Seed');
    return $this->imageable();
  }

  public function imageable()
    {
      return $this->morphTo();
    }


  /**
   * Convert and save uploaded image
   * @param UploadedImage
   * @return Picture or false
   */
  static function fromUploadedFile($uploadedimage){

    $file_md5 = md5_file($uploadedimage);
    $picture_path = storage_path('pictures');
    $file_name = $file_md5 . '.jpg';
    $file_path = $picture_path . '/' . $file_name;

    while ( file_exists($file_path) ) {
      $file_name = $file_md5 . '_' . str_random(3) . '.jpg';
      $file_path = $picture_path . '/' . $file_name;
    }

    $uploadedimage->move($picture_path, $file_name);
    $converted_image = new \Imagick($file_path);
    $converted_image->setImageFormat('jpg');
    $converted_image->scaleimage(800, 800, true);
    if (filesize($file_path) > 200000) {
      $converted_image->setOption('jpeg:extent', '100kb');
    }
    $status = $converted_image->writeimage($file_path);
    if ($status) {
      $picture = Picture::create([
        'path' => $file_path,
        'url' => '/seedbank/pictures/' . $file_name,
        //TODO: eliminate following line
        'label' => '',
        'md5sum' => $file_md5
      ]);
    } else {
      // return [ "error" => "File not saved"];
      $picture = false;
    }

    return $picture;
  }

}
