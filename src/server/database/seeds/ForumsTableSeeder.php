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
        'category_id' => 0,
        'title' => 'Site',
        'description' => 'Discussão à volta do site',
        'weight' => 20,
        'enable_threads' => 1,
        'private' => 1,
        'created_at' => \Carbon\Carbon::now(),
        'updated_at' => \Carbon\Carbon::now(),
      ]);
      $category->save();
    }
}
