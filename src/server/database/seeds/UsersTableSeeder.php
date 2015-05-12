<?php
use Illuminate\Database\Seeder;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\Registrar;





class UsersTableSeeder extends Seeder {


	public function __construct(Registrar $registrar)
	{
		$this->registrar = $registrar;

	}

  public function run()
  {
    DB::table('users')->delete();
    DB::table('role_user')->delete();

    $adminId = Caravel\Role::where('name', 'admin')->first()->id;
    $editorId = Caravel\Role::where('name', 'editor')->first()->id;
    $user = $this->registrar->create([
    	'name' => 'Devel User',
    	'email' => 'devel@example.com',
    	'password' => 'develuser',
    ]);

    $user->roles()->attach($adminId);
    $user->confirmed = 1;
    $user->save();

    $faker = Faker\Factory::create();

    for($i = 0; $i < 10; $i++){
    	$user = $this->registrar->create([
    		'name' => $faker->name,
     		'email' => $faker->email,
     		'password' => $faker->word,
     	]);
      $user->roles()->attach($editorId);
      $user->confirmationString = substr(sha1(rand()), 0, 32);
      $user->confirmed = $faker->randomElement([0, 1]);
      $user->save();
    }
    $this->command->info("User table seeded!\nYou can login with user: devel@example.com pass: develuser");
  }
}
