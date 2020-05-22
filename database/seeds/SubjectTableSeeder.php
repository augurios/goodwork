<?php

use App\Base\Models\Subject;
use Illuminate\Database\Seeder;

class SubjectTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Subject::create([
            'name'       => 'Violencia doméstica',
        ]);
        Subject::create([
            'name'       => 'Violencia intrafamiliar',
        ]);
        Subject::create([
            'name'       => 'Violencia sexual',
        ]);
        Subject::create([
            'name'       => 'Hostigamiento sexual',
        ]);
        Subject::create([
            'name'       => 'Acoso sexual',
        ]);
        Subject::create([
            'name'       => 'Other',
        ]);
    }
}