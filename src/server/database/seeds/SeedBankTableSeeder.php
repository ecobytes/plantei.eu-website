<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class SeedBankTableSeeder extends Seeder
{

    public function randomTransactionBy($user_id){
        $user = \Caravel\User::find($user_id);
        $exchange = false;
        while(!$exchange){
            $seed = false;
            while (!$seed){
                $seed = \Caravel\Seed::find(random_int(1,20));
                if ($seed){
                    if ($seed->user_id == $user->id){ $seed = false;}
                }
            }

            $data = [
                'asked_by' => $user->id,
                'asked_to' => $seed->user_id, 
                'seed_id' => $seed->id
            ];
            if (\Caravel\SeedsExchange::where($data)->first())
            {
                $exchange = false;
            } else {
                $exchange = $user->startTransaction([
                    'asked_to'=>$seed->user_id, 
                    'seed_ids'=> [$seed->id]
                ])->first();
            }
        }
        return $exchange;
    }
    public function randomTransactionTo($user_id){
        $user = false;
        $seeds = \Caravel\User::find($user_id)->seeds()->get();
        $exchange = false;
        while(!$exchange){
            while (!$user){
                $user = \Caravel\User::find(random_int(2,11));
            }
            $rindex = random_int(0,$seeds->count() - 1);
            $seed = $seeds[$rindex];
            $data = [
                'asked_by' => $user->id,
                'asked_to' => $user_id, 
                'seed_id' => $seed->id
            ];
            if (\Caravel\SeedsExchange::where($data)->first())
            {
                $exchange = false;
                $user = false;
            } else {
                $exchange = $user->startTransaction([
                    'asked_to'=>$user_id,
                    'seed_ids'=>[$seed->id]
                ])->first();
            }
        }
        return $exchange;
    }
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker\Factory::create();
        DB::table('family')->insert([
            ['name' => 'family' . str_random(5), 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'family' . str_random(5), 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'family' . str_random(5), 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]
        ]);
        foreach (DB::table('family')->get() as $family){
            DB::table('species')->insert([
                ['name' => 'species' . str_random(5), 'family_id' => $family->id, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
                ['name' => 'species' . str_random(5), 'family_id' => $family->id, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
                ['name' => 'species' . str_random(5), 'family_id' => $family->id, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]
            ]);
        };
        foreach (DB::table('species')->get() as $specie){
            DB::table('variety')->insert([
                ['name' => 'variety' . str_random(5), 'species_id' => $specie->id, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
                ['name' => 'variety' . str_random(5), 'species_id' => $specie->id, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
                ['name' => 'variety' . str_random(5), 'species_id' => $specie->id, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]
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
                    $seed = Caravel\Seed::firstOrCreate(
                        [
                            'common_name' => 'common_name' . str_random(5),
                            'local' => 'local' . str_random(3),
                            //'origin' => random_int(1,3),
                            'year' => random_int(2010,2015),
                            'description' => "description " . $faker->text(500),
                            'available' => true,
                            'public' => true,
                            'user_id' => random_int(1,10),
                            'latin_name' => 'latin_name' . str_random(5),
                            'species_id' => $specie->id,
                            'variety_id' => $variety->id,
                            'family_id' => $family->id,
                            'polinization' => true, 
                            'direct' => false
                        ]);

                    $seed1 = Caravel\Seed::firstOrCreate(
                        [
                            'common_name' => 'common_name' . str_random(5),
                            'local' => 'local' . str_random(3),
                            //'origin' => random_int(1,3),
                            'year' => random_int(2010,2015),
                            'description' => "description " . $faker->text(500),
                            'available' => true,
                            'public' => true,
                            'user_id' => 1,
                            'latin_name' => 'latin_name' . str_random(5),
                            'species_id' => $specie->id,
                            'variety_id' => $variety->id,
                            'family_id' => $family->id,
                            'polinization' => true, 
                            'direct' => false
                        ]);
                    $seed_months = $seed->months()->saveMany(
                        [
                            new Caravel\SeedMonth(['month' => random_int(1,12)]),
                            new Caravel\SeedMonth(['month' => random_int(1,12)]),
                        ]);

                };
            };
        };
        $u = \Caravel\User::find(1);
        $t = $this->randomTransactionTo($u->id);
        $t = $this->randomTransactionBy($u->id);
        $t = $this->randomTransactionBy($u->id);
        $u->rejectTransaction($t->id); 
        $t = $this->randomTransactionTo($u->id);
        $u->acceptTransaction($t->id); 
        $t = $this->randomTransactionBy($u->id);
        $t->update(['accepted' => 2]);
        $u->completeTransaction($t->id);
        $t = $this->randomTransactionTo($u->id);
        $u->rejectTransaction($t->id);
        $t = $this->randomTransactionTo($u->id);
        $t->update(['completed' => 2]);
        $u->acceptTransaction($t->id);
        $t = $this->randomTransactionBy($u->id);
        $t->update(['accepted' => 2]);
        $t->updateParent();
    }
}
