<?php
use Illuminate\Database\Seeder;


class RolesTableSeeder extends Seeder {

    public function run()
    {
        DB::table('roles')->delete();

        \Caravel\Role::create(['name' => 'admin']);
        \Caravel\Role::create(['name' => 'user']);
    }

}
