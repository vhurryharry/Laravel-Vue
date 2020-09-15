<?php

// use Illuminate\Database\Seeder;
use Flynsarmy\CsvSeeder\CsvSeeder;

class ActivityValuesTableSeeder extends CsvSeeder
{
    public function __construct()
    {
        $this->table = 'activities_values';
        // $this->filename = base_path() . '/database/seeds/csvs/privacy_values.csv';
        $this->filename = base_path() . '/database/seeds/csvs/activity_values.csv';

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