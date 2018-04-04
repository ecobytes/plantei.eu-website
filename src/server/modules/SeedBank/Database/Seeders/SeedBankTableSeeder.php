<?php namespace Modules\Seedbank\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class SeedBankTableSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Model::unguard();

		// $this->call("OthersTableSeeder");

		$faker = \Faker\Factory::create();

		// Fake 10 sementecas
		// limites do mapa
    // norte: 44.074958, -9.997559
		// sul: 36.689565, -6.04248
		$latRange = [36.689565, 44.074958];
		$lonRange = [-9.997559, -6.04248];
		$a = 0;
		while ($a <= 10) {
			$lon = mt_rand($lonRange[0]*1000000,$lonRange[1]*1000000)/1000000;
			$lat = mt_rand($latRange[0]*1000000,$latRange[1]*1000000)/1000000;
			$sementeca = \Caravel\Sementeca::create([
				'contact' => $faker->email(),
				'address' => $faker->address(),
				'lat' => $lat,
				'lon' => $lon,
				'name' => $faker->name(),
				'description' => $faker->text(200),
				'active' => true
			]);
			$a += 1;
		}
		$a -= 1;
		$this->command->info("Sementecas table seeded with $a items!");


		// Fake 50 events
		// 20 this month
		// 30 next month

		$latRange = [36.689565, 44.074958];
		$lonRange = [-9.997559, -6.04248];
		$categories = ['evento', 'noticia', 'coisas'];
		$datePadron = \Carbon\Carbon::now()->startOfMonth();

    $a = 0;
		while ($a <= 50) {
			$lon = mt_rand($lonRange[0]*1000000,$lonRange[1]*1000000)/1000000;
			$lat = mt_rand($latRange[0]*1000000,$latRange[1]*1000000)/1000000;
			$date = new \Carbon\Carbon($datePadron);
			if ($a > 30){
				$date = $date->addMonths(1);
			}

			$date->addDays(random_int(1,30))->hour(random_int(10,21));

			$event = \Caravel\Calendar::create([
				'lat' => $lat,
				'lon' => $lon,
				'title' => $faker->name(),
				'description' => $faker->text(200),
				'category' => $categories[mt_rand(0, count($categories) - 1)],
				'user_id' => 1,
				'start' => $date->toDateTimeString(),
				'end' => $date->addHours(random_int(1,5))->toDateTimeString(),
				'location' => $faker->city(),
				//'postal' => $faker->postcode(),
				'address' => $faker->streetAddress(),
				'image' => 'fakeimage'
			]);
			$a += 1;
		}
		$a -= 1;
		$this->command->info("Events table seeded with $a items!");



	}

}
