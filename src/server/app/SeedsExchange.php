<?php

namespace Caravel;

use Illuminate\Database\Eloquent\Model;

class SeedsExchange extends Model
{
    //
    protected $fillable = ['asked_to', 'asked_by', 'seed_id'];

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'seeds_exchanges';

    /**
     * Accept transaction.
     * 
     * @return void
     */
    public function transactionAccepted()
	{
        $this->update(['accepted' => true]);
	}

    /**
     * Reject transaction.
     * 
     * @return void
     */
    public function transactionRejected()
	{
        $this->update(['accepted' => false]);
	}

    /**
     * Complete transaction.
     *
     * @return void
     */
    //TODO: create and return the new SeedBank
    public function transactionCompleted()
	{
        $this->update(['completed' => true]);
	}

}
