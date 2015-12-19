<?php

namespace Caravel;

use Illuminate\Database\Eloquent\Model;

class SeedsExchange extends Model
{
    //
    protected $fillable = ['asked_to', 'asked_by', 'seed_id', 'parent_id', 'accepted', 'completed'];
    //protected $touches = ['parent'];

	/**
	 * The database table used by the model.
	 *
	 * @var string
     */
	protected $table = 'seeds_exchanges';


    public function childs()
    {
        if ($this->parent_id)
        {
            return $this->parent->childs();
        }
        return $this->hasMany('Caravel\SeedsExchange', 'parent_id', 'id');
    }

    public function parent()
    {
        if ($this->parent_id)
        {
            return $this->belongsTo('Caravel\SeedsExchange', 'parent_id');
        }
        //return $this;
    }

    public function updateParent()
    {
        $parent = $this->parent;
	    $accepted = true;
        $rejected = true;
	    $cancelled = true;
	    $completed = true;
	    foreach ($this->childs as $child)
	    {
	      if (($child->accepted != 2)) { $accepted = false; }
	      if (($child->accepted != 1)) { $rejected = false; }
	      if (($child->completed != 1)) { $cancelled = false; }
	      if (($child->completed != 2)) { $completed = false; }
	    }
	    if ($accepted) { $parent->update(['accepted' => 2]); }
	    if ($rejected) { $parent->update(['accepted' => 1]); }
	    if ($cancelled) { $parent->update(['completed' => 1]); }
	    if ($completed) { $parent->update(['completed' => 2]); }
        return $parent;
    }
}
