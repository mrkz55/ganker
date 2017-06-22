<?php

use Illuminate\Database\Seeder;
//use App\app\User as User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

      DB::table('users')->delete();

      DB::table('users')->insert([
                                 'created_at' => date("Y-m-d G:i:s", time()),
                                 'updated_at' => date("Y-m-d G:i:s", time()),
                                 'name'          => 'mark',
                                 'email'   => 'mark@gmail.com',
                                 'password'         => Hash::make('gr0per1969'),

                                 ]);


    }
  }