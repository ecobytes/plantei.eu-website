<?php

namespace Caravel;

use Illuminate\Database\Eloquent\Model;

class Encilopedia extends Model
{
  protected $table = 'enciclopedias';

  public function pictures()
  {
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
  public function references()
  {
    return $this->morphMany('Caravel\Reference', 'referenceable');
  }

}

class Popname extends Model
{
  protected $table = 'popnames';

  public function popnameable()
  {
    return $this->morphTo();
  }
}

class Reference extends Model
{
  protected $table = 'references';
  public function referenceable()
  {
    return $this->morphTo();
  }
}

class PlantUsage extends Model
{
  protected $table = 'plantuses';
  public function plantuseable()
  {
    return $this->morphTo();
  }
}
