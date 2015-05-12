<?php namespace Caravel;

use Illuminate\Database\Eloquent\Model;

class Role extends Model {

	protected $table = 'roles';

	protected $fillable = ['name'];

  public function users(){
  	return $this->belongsToMany('Caravel\User');
  }
}
