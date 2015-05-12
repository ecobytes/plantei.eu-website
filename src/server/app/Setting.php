<?php namespace Caravel;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model {

protected $table = 'settings';
public $timestamps = false;

protected $fillable = ['value'];

}
