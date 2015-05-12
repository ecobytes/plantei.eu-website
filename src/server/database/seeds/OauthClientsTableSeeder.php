<?php
use Illuminate\Database\Seeder;


class OauthClientsTableSeeder extends Seeder {


  public function run()
  {
    DB::table('oauth_clients')->delete();
    DB::insert('insert into oauth_clients (secret, name, created_at, updated_at) values (?, ?, ?, ?)', [env('OAUTH_CLIENT_SECRET'), 'baseClient', 'NOW()', 'NOW()']);
  }
}
