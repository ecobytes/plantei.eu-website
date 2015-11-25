<?php

namespace Caravel;

use Illuminate\Database\Eloquent\Model;

class SeedsBank extends Model
{
	protected $fillable = ['local', 'origin', 'year', 'user_id', 'seed_id', 'available', 'description'];
	
}
