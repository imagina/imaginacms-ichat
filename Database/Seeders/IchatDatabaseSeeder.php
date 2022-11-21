<?php

namespace Modules\Ichat\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class IchatDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->call(IchatModuleTableSeeder::class);
        // $this->call("OthersTableSeeder");
    }
}
