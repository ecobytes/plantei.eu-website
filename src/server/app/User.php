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
	  return $this->hasMany('Caravel\Seed', 'user_id');
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
     * Get pending transactions.
     * @param $limit(integer), $orderBy(string), $toArray(boolean)
     * @return array
     */
    public function transactionsPending($limit=10, $orderBy='seeds_exchanges.updated_at', $toArray=true)
	{
	  //Transactions started by other
	  $askedTo = $this->hasMany('Caravel\SeedsExchange', 'asked_to')
		->whereNotNull('parent_id')
		->where(function ($query) {
		  $query->where('completed', false)->orWhere('completed', null);
		})
		->join('users', 'users.id', '=', 'asked_by')
		->join('seeds', 'seeds.id', '=', 'seeds_exchanges.seed_id');
		//->select('seeds_exchanges.*');
	  //Transactions started by self
	  $askedBy = $this->hasMany('Caravel\SeedsExchange', 'asked_by')
		->whereNotNull('parent_id')
		->where( function ($query) {
		  $query->where('completed', false)->orWhere('completed', null);
		})
		->join('users', 'users.id', '=', 'asked_to')
		->join('seeds', 'seeds.id', '=', 'seeds_exchanges.seed_id');
		//->select('seeds_exchanges.*', );
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
    public function startTransaction($data)
	{
	  if ((! isset($data['asked_to'])) || ( ! isset($data['seed_ids']) ))
	  {
		return false;
	  }
	  if ( ! is_array($data['seed_ids'])) { return false; }
      $data['asked_by'] = $this->id;
      $seed_ids = array_pull($data, 'seed_ids');
      if ( ! isset($data['parent_id']) )
      {
        $parent = SeedsExchange::create($data);
	  } else {
		$parent = SeedsExchange::findOrFail($data['parent_id']);
	  }
	  foreach ($seed_ids as $seed_id)
	  {
        $data['seed_id'] = $seed_id;
        $data['parent_id'] = $parent->id;
		$transaction = SeedsExchange::create($data);
	  }
      return $parent;
	}


    /**
     * Accept transaction.
     * 
     * @return void
     */
    public function acceptTransaction(SeedsExchange $transaction)
	{
	  if ( $transaction->asked_to != $this->id)
	  {
		return false;
	  }
	  if ( ! $transaction->parent_id )
	  {
		foreach ($transaction->childs()->get() as $child)
		{
		  $child->update(['accepted' => true]);
		}
		$transaction->update(['accepted' => true]);
	  } else {
		$transaction->update(['accepted' => true]);
	  }
	  return $transaction->updateParent();
	}

    /**
     * Reject transaction.
     * 
     * @return void
     */
    public function rejectTransaction(SeedsExchange $transaction)
	{
	  if ( $transaction->asked_to == $this->id)
	  {
	    if ( ! $transaction->parent_id )
	    {
	      foreach ($transaction->childs()->get() as $child)
	      {
	        $child->update(['accepted' => false]);
	      }
	      $transaction->update(['accepted' => false]);
	    } else {
	      $transaction->update(['accepted' => false]);
	    }
	    return $transaction->updateParent();
	  } 
	  if ( $transaction->asked_by == $this->id)
	  {
	    if ( ! $transaction->parent_id )
	    {
	      foreach ($transaction->childs()->get() as $child)
	      {
	        $child->update(['completed' => false]);
	      }
	      $transaction->update(['completed' => false]);
	    } else {
	      $transaction->update(['completed' => false]);
	    }
	    return $transaction->updateParent();
	  }
      return false;

	}

    /**
     * Complete transaction.
     *
     * @return void
     */
    public function completeTransaction(SeedsExchange $transaction)
	{
	  if ( $transaction->asked_by != $this->id )
	  {
		return false;
	  }
	  if ( ! $transaction->parent_id )
	  {
	    foreach ($transaction->childs()->get() as $child)
	    {
	      $child->update(['completed' => true]);
	    }
	    $transaction->update(['completed' => true]);
	  } else {
	    $transaction->update(['completed' => true]);
	  }
	  return $transaction->updateParent();
	}

}
