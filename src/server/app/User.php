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
	  ->withPivot('read', 'replied');
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

    /**
     * Get all sent messages.
     *
     * @return HasMany
     */
    public function sentMessages()
    {
        return $this->hasMany('Caravel\Message', 'user_id');
    }

    /**
     * Get all user messages.
     *
     * @return HasMany
     */
    public function lastMessages($limit=10)
	{
	  $col = \Caravel\Message::select(\DB::raw('*, messages.user_id as sender_id'))
		->join('message_user', 'message_user.message_id', '=', 'messages.id')
		->where('message_user.user_id', $this->id)
		->orWhere('messages.user_id', $this->id)
		->orderBy('created_at', 'desc')
		->limit($limit)->get();
		//->get()->sortByDesc('created_at')->forPage($page, $chunks);

	  
	 /* $sent = $this->sentMessages()->get();
	  $received = $this->messages()->get();
	  $all = $sent->merge($received)
		->sortByDesc('created_at')
		->groupBy('root_message_id')
		->forPage($page, $chunks);
	  /*if ($all->count() > $page + 1
		[$page]->sortBy('created_at');*/
	  return $col;
    }

    /**
     * Get latest user messages.
     *
     * @return HasMany
     */
 /*   public function latestMessages($limit=6)
	{
	  $latest = $this->messages()
		->orderBy('created_at', 'desc')
		->take($limit)->get();
	  return $latest;
    }
	  */
}
