<?php

// use Illuminate\Database\Seeder;
use Flynsarmy\CsvSeeder\CsvSeeder;

class GendersTableSeeder extends CsvSeeder
{
    public function __construct()
    {
        $this->table = 'gender_values';
        $this->filename = base_path() . '/database/seeds/csvs/genders.csv';
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
