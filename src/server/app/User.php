<?php namespace Caravel;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Model implements AuthenticatableContract,
                                    AuthorizableContract,
									CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword;

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

	public function messages()
  {
	return $this->belongsToMany('Caravel\Message', 'message_user')
	  //->sortByDesc('created_at')
	  ->withPivot('read', 'replied', 'root_message_id');
  }

    public function messageById($id)
    {
      if (! $id){
        return false;
      }
      return $this->messages()->where('id', $id)->first();
    }
	public function getIdAttribute($value) {
			return (int) $value;
	}

	public function getseeds() {
	  // TODO: Replace DB::query by Eloquent model Query
	  $seedbank = \DB::table('seeds')->join('seeds_banks', 'seeds_banks.seed_id', '=', 'seeds.id')
		->where('user_id', $this->id)->get();
	  $result = array();
	  foreach($seedbank as $i){
		$result[] = (array)$i;
	  };
		
			return $result;
	}
}
