<?php namespace Modules\Newsletter\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class NewsletterDatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Model::unguard();
		
		\DB::table('newsletter_subscriptors')->delete();

    $faker = \Faker\Factory::create();

    for($i = 0; $i < 100; $i++){
    	$subscriptor = \Modules\Newsletter\Entities\NewsletterSubscriptor::create([
    		'name' => $faker->name,
     		'email' => $faker->email,
     		'verified' => $faker->randomElement([0, 1]),
     		'verification_key' => substr(sha1(rand()), 0, 32),
     		'active' => $faker->randomElement([0, 1])
     	]);
    }

	}

}