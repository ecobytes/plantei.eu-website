<?php

use Illuminate\Database\Seeder;

class SeedBankTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('family')->insert([
            ['name' => 'family' . str_random(5)],
            ['name' => 'family' . str_random(5)],
            ['name' => 'family' . str_random(5)]
        ]);
        foreach (DB::table('family')->get() as $family){
            DB::table('species')->insert([
                ['name' => 'species' . str_random(5), 'family_id' => $family->id],
                ['name' => 'species' . str_random(5), 'family_id' => $family->id],
                ['name' => 'species' . str_random(5), 'family_id' => $family->id]
            ]);
        };
        foreach (DB::table('species')->get() as $specie){
            DB::table('variety')->insert([
                ['name' => 'variety' . str_random(5), 'species_id' => $specie->id],
                ['name' => 'variety' . str_random(5), 'species_id' => $specie->id],
                ['name' => 'variety' . str_random(5), 'species_id' => $specie->id]
            ]);
        };
        foreach (DB::table('family')->get() as $family) {
            foreach (DB::table('species')->where('family_id', $family->id)->get() as $specie) {
                foreach (DB::table('variety')->where('species_id', $specie->id)->get() as $variety) {
                    /*DB::table('seeds')->insert([
                        'sci_name' => 'sci_name' . str_random(5), 
                        'common_name' => 'common_name' . str_random(5), 
                        'polinization' => true, 'direct' => false,
                        'species_id' => $specie->id,
                        'variety_id' => $variety->id,
                        'family_id' => $family->id
                    ]);*/
                    $seed = Caravel\Seed::firstOrCreate([
			'sci_name' => 'sci_name' . str_random(5), 
                        'common_name' => 'common_name' . str_random(5), 
                        'polinization' => true, 'direct' => false,
                        'species_id' => $specie->id,
                        'variety_id' => $variety->id,
                        'family_id' => $family->id
		    ]);
                DB::table('seed_months')->insert([
                    'seed_id' => $seed->id,
                    'month' => random_int(1,12),
                    ]);
                DB::table('seed_months')->insert([
                    'seed_id' => $seed->id,
                    'month' => random_int(1,12),
                    ]);

                };
            };
        };
        foreach (\Caravel\Seed::get() as $seed){
            $user = \Caravel\User::find(random_int(1,10));
            $seeds_bank = Caravel\SeedsBank::firstOrCreate([
                    'local' => 'local' . str_random(3),
                    'origin' => random_int(1,3),
                    'year' => random_int(2010,2015),
                    'description' => "description " . str_random(221),
                    'available' => true,
                    'public' => true,
                    'user_id' => $user->id,
                    'seed_id' => $seed->id
            ]);
        };
    }
}
