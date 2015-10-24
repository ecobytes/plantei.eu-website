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
                    DB::table('seeds')->insert([
                        'sci_name' => 'sci_name' . str_random(5), 
                        'common_name' => 'common_name' . str_random(5), 
                        'polinization' => true, 'direct' => false,
                        'species_id' => $specie->id,
                        'variety_id' => $variety->id,
                        'family_id' => $family->id
                    ]);
                };
            };
        };
        $count = 1;
        foreach ([1,2,3,4] as $i){
            $user = \Caravel\User::find($i);
            $count++;
            foreach([1,2,3,4] as $j){
                $seed = DB::table('seeds')->where('id', $j + $count)->first();
                DB::table('seeds_bank')->insert([
                    'local' => 'local' . str_random(3),
                        'origin' => 1,
                        'year' => 1920,
                        'description' => "description " . str_random(21),
                        'available' => true,
                        'public' => true,
                        'user_id' => $user->id,
                        'seed_id' => $seed->id
                    ]);
                DB::table('seed_months')->insert([
                    'seed_id' => $seed->id,
                    'month' => 6,
                    ]);
                DB::table('seed_months')->insert([
                    'seed_id' => $seed->id,
                    'month' => 9,
                    ]);



            };
        };
    }
}
