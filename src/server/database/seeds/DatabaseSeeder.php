<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Model::unguard();
		$this->call('SettingTableSeeder');
		$this->call('RolesTableSeeder');
		$this->call('UsersTableSeeder');
		#$this->call('OauthClientsTableSeeder');
		#$this->call('SeedBankTableSeeder');
		$this->call('ForumsTableSeeder');
	}

}
