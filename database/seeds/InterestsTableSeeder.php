<?php

// use Illuminate\Database\Seeder;
use Flynsarmy\CsvSeeder\CsvSeeder;

class InterestsTableSeeder extends CsvSeeder
{
    public function __construct()
    {
        $this->table = 'interests';
        $this->filename = base_path() . '/database/seeds/csvs/interests.csv';
    }

    public function run()
    {
        // Recommended when importing larger CSVs
        // DB::disableQueryLog();

        // Uncomment the below to wipe the table clean before populating
        // DB::table($this->table)->truncate();

        parent::run();
    }
}
