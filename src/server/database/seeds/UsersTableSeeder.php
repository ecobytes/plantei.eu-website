<?php
use Illuminate\Database\Seeder;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\Registrar;





class UsersTableSeeder extends Seeder {


	public function __construct(Registrar $registrar)
	{
		$this->registrar = $registrar;

	}

	public function create(array $data)
	{
		foreach ($data as $key => $value){
			if ((!$value) || (!in_array($key, ['name', 'email', 'password', 'lon', 'lat', 'place_name']))){
				unset($data[$key]);
			}
		}
		$data['password'] = bcrypt($data['password']);
		//dd($data);
		return Caravel\User::create($data);
			/*[
			'name' => $data['name'],
			'email' => $data['email'],
			'password' => bcrypt($data['password']),
			'lon' => $data['lon'],
			'lat' => $data['lat'],
			'place_name' => $data['place_name'],
		]);*/
	}
  public function run()
  {
    DB::table('users')->delete();
    DB::table('role_user')->delete();

    $adminId = Caravel\Role::where('name', 'admin')->first()->id;
    $userId = Caravel\Role::where('name', 'user')->first()->id;
    $user = $this->create([
    	'name' => 'Devel User',
    	'email' => 'devel@example.com',
    	'password' => 'develuser',
    	'place_name' => 'Somewhere on planet Earth',
    	'lat' => '30.2',
    	'lon' => '-9.1',
    ]);

    $user->roles()->attach($adminId);
    $user->confirmed = 1;
    $user->save();

    $faker = Faker\Factory::create();

    for($i = 0; $i < 10; $i++){
    	$user = $this->create([
    		'name' => $faker->name,
     		'email' => $faker->email,
     		'password' => $faker->word,
            'lat' => strval($faker->randomFloat($nbMaxDecimals = 3, $min = -179, $max = 179)),
            'lon' => strval($faker->randomFloat($nbMaxDecimals = 3, $min = -179, $max = 179)),
            'place_name' => $faker->city,
     	]);
      $user->roles()->attach($userId);
      $user->confirmationString = substr(sha1(rand()), 0, 32);
      $user->confirmed = $faker->randomElement([0, 1]);
      $user->save();
    }
    $this->command->info("User table seeded!\nYou can login with user: devel@example.com pass: develuser");
  }
}
