<?php

use Illuminate\Database\Seeder;

class MessageTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker\Factory::create();
        $counter = 0;
        foreach (Caravel\User::get() as $user) {
            for($i = 0; $i < 3; $i++){
                $message = Caravel\Message::firstOrCreate([
                    'subject' => $faker->sentence, 
                    'body' => $faker->paragraph, 
                    'user_id' => $user->id,
                ]);
                $message->root_message_id = $message->id;
                $message->save();
                if (($user->id != 1) && ($counter<5)){
                    $counter++;
                    $message->users()->attach(1);
                }

            }
        };
        $user = Caravel\User::find(1);
        foreach ($user->messages as $message)
        {
            $message->reply(['body' => $faker->paragraph(10)]);

        };

    }
}
