<?php

use Illuminate\Database\Seeder;

class MessagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      DB::table('threads')->delete();
      $user = \Caravel\User::find(1);
      $thread = \Cmgmyr\Messenger\Models\Thread::create([
        'subject' => 'a message subject',
        'created_at' => \Carbon\Carbon::now(),
        'updated_at' => \Carbon\Carbon::now(),
      ]);
      $thread->save();

      DB::table('messages')->delete();
      $message = \Cmgmyr\Messenger\Models\Message::create([
        'user_id' => $user->id,
        'thread_id' => $thread->id,
        'body' => 'mess age a body a message subject',
        'created_at' => \Carbon\Carbon::now(),
        'updated_at' => \Carbon\Carbon::now(),
      ]);
      $message->save();

      DB::table('participants')->delete();
      $participant = \Cmgmyr\Messenger\Models\Participant::create([
        'user_id' => $user->id,
        'thread_id' => $thread->id,
        'created_at' => \Carbon\Carbon::now(),
        'updated_at' => \Carbon\Carbon::now(),
      ]);
      $participant->save();

    }
}
