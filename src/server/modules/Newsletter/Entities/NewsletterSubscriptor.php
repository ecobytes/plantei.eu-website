<?php namespace Modules\Newsletter\Entities;
   
use Illuminate\Database\Eloquent\Model;

class NewsletterSubscriptor extends Model {

	protected $table = 'newsletter_subscriptors';
	protected $fillable = ['name', 'email'];
}