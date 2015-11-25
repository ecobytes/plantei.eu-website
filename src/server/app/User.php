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
	  $message = $this->messages()->where('id', $id)->first();
	  if (!$message){
		$message = \Caravel\Message::findOrFail($id);
		if ($message->user_id != $this->id){
		  abort(403);
		} else {
		  $message['read'] = true;
		  $message['replied'] = true;
		}
	  }
	  return $message;
    }
	public function getIdAttribute($value) {
			return (int) $value;
	}

	public function seeds() {
	  return $this->hasMany('Caravel\SeedsBank', 'user_id')
		->join('seeds', 'seeds.id', '=', 'seed_id')
		->select('seeds.*', 'seeds.description as root_description', 'seeds.id as seed_id', 'seeds_banks.*')
		->get()->toArray();
	}

    /**
     * Get all sent messages.
     *
     * @return Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sentMessages()
    {
        return $this->hasMany('Caravel\Message', 'user_id');
    }

    /**
     * Get last messages.
     * @param integer
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function lastMessages($limit=10)
	{
	  return Message::select(\DB::raw('*, messages.user_id as sender_id'))
		->join('message_user', 'message_user.message_id', '=', 'messages.id')
		->where('message_user.user_id', $this->id)
		->orWhere('messages.user_id', $this->id)
		->orderBy('created_at', 'desc')
		->limit($limit)->get();
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
    /**
     * Get pending transactions.
     * @param $limit(integer), $orderBy(string), $toArray(boolean)
     * @return array
     */
    public function transactionsPending($limit=10, $orderBy='updated_at', $toArray=true)
	{
	  //Transactions started by other
	  $askedTo = $this->hasMany('Caravel\SeedsExchange', 'asked_to')
		->join('users', 'users.id', '=', 'asked_by')
		->join('seeds', 'seeds.id', '=', 'seeds_exchanges.seed_id')
		->select('seeds_exchanges.*', 'users.name', 'users.place_name', 'users.email', 'seeds.common_name', 'seeds.sci_name');
		//->get()->toArray();
	  //Transactions started by self
	  $askedBy = $this->hasMany('Caravel\SeedsExchange', 'asked_by')
		->where('completed', false)
		->join('users', 'users.id', '=', 'asked_to')
		->join('seeds', 'seeds.id', '=', 'seeds_exchanges.seed_id')
		->select('seeds_exchanges.*', 'users.name', 'users.place_name', 'users.email', 'seeds.common_name', 'seeds.sci_name');
	  //->get()->toArray();
	  if ($orderBy)
	  { 
		$askedTo = $askedTo->orderBy($orderBy, 'desc'); 
		$askedBy = $askedBy->orderBy($orderBy, 'desc'); 
	  }
	  $askedTo = $askedTo->limit($limit);
	  $askedBy = $askedBy->limit($limit);
	  if ($toArray)
	  { 
		$askedTo = $askedTo->get()->toArray();
		$askedBy = $askedBy->get()->toArray(); 
	  }


	  return ['asked_to' => $askedTo, 'asked_by' => $askedBy];
    }

    /**
     * Get pending transactions.
     * @param  integer
     * @return Collection
     */
    public function transactionsLatest($limit=10)
	{
		return SeedsExchange::where('asked_to', $this->user_id)
			->orWhere('asked_by', $this->user_id)
			->orderByDesc('updated_at')
			->limit($limit)
			->get();
	}

    /**
     * Start a new transaction.
     * @param  array
     * @return Caravel\SeedExchange
     */
	// TODO: maybe @param should be the destination seedbank Caravel\SeedsBank
    public function transactionStart($data)
	{
	  if ((! isset($data['asked_to'])) || (! isset($data['seed_id'])))
	  {
		return false;
	  }
	  $data['asked_by'] = $this->id;

	  return SeedsExchange::firstOrCreate($data);
	}

}
