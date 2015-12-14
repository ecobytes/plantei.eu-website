<?php

namespace Caravel;

use Illuminate\Database\Eloquent\Model;

class SeedsExchange extends Model
{
    //
    protected $fillable = ['asked_to', 'asked_by', 'seed_id', 'parent_id'];
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
            return $this->parent->childs;
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
	    foreach ($this->childs() as $child)
	    {
	      if (($child->accepted != true)) { $accepted = false; }
	      if (($child->accepted != false)) { $rejected = false; }
	      if (($child->completed != false)) { $cancelled = false; }
	      if (($child->completed != true)) { $completed = false; }
	    }
	    if ($accepted) { $parent->update(['accepted' => true]); }
	    if ($rejected) { $parent->update(['accepted' => false]); }
	    if ($cancelled) { $parent->update(['completed' => false]); }
	    if ($completed) { $parent->update(['completed' => true]); }
        return $parent;
    }
}
