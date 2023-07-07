<?php

namespace Database\Seeders;

use App\Models\Employee;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // DB::table('employees')->insert([
        //     [
        //         'firstname' => 'Purnama',
        //         'lastname' => 'Anaking',
        //         'email'=> 'purnama.anaking@gmail.com',
        //         'age' => 20,
        //         'position_id' => 1
        //     ],

        //     [
        //         'firstname' => 'Adzro',
        //         'lastname' => 'Salsabilla',
        //         'email'=> 'adzro.rusya03@gmail.com',
        //         'age' => 20,
        //         'position_id' => 2
        //     ],

        //     [
        //         'firstname' => 'Moch.Aditya',
        //         'lastname' => 'Pramana',
        //         'email'=> 'adit.pram@gmail.com',
        //         'age' => 21,
        //         'position_id' => 3
        //     ],
        // ]);

        Employee::factory()->count(200)->create();
    }
}

?>

