<?php namespace Caravel;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract {

	use Authenticatable, CanResetPassword;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['name', 'email', 'password', 'lat', 'lon', 'place_name'];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = ['password', 'remember_token'];


	public function roles()
  {
    return $this->belongsToMany('Caravel\Role');
  }

	public function getIdAttribute($value) {
			return (int) $value;
	}

	public function getseeds() {
	  $seedbank = \DB::table('seeds')->join('seeds_bank', 'seeds_bank.seed_id', '=', 'seeds.id')
		->where('user_id', $this->id)->get();
	  $result = array();
	  foreach($seedbank as $i){
		$result[] = (array)$i;
	  };
		
			return $result;
	}
}
