<?php

use Illuminate\Database\Seeder;

class ForumsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      DB::table('forum_categories')->delete();
      $user = \Caravel\User::find(1);
      $category = \Riari\Forum\Models\Category::create([
        'title' => 'Site',
        'description' => 'Discussão à volta do site',
        'weight' => 20,
        'enable_threads' => 1,
        'private' => 1,
        'created_at' => \Carbon\Carbon::now(),
        'updated_at' => \Carbon\Carbon::now(),
      ]);
      $category->save();

      DB::table('forum_threads')->delete();
      $thread = \Riari\Forum\Models\Thread::create([
        'category_id' => $category->id,
        'author_id' => 1,
        'title' => 'Uma discussão',
        'created_at' => \Carbon\Carbon::now(),
        'updated_at' => \Carbon\Carbon::now(),
      ]);
      $thread->save();

      DB::table('forum_posts')->delete();
      $post = \Riari\Forum\Models\Post::create([
        'thread_id' => $thread->id,
        'author_id' => $user->id,
        'content' => 'Conteúdo do post',
        'created_at' => \Carbon\Carbon::now(),
        'updated_at' => \Carbon\Carbon::now(),
      ]);
      $post->save();

    }
}
