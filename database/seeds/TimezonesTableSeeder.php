<?php

// use Illuminate\Database\Seeder;
use Flynsarmy\CsvSeeder\CsvSeeder;

class TimezonesTableSeeder extends CsvSeeder
{
    public function __construct()
    {
        $this->table = 'timezone_values';
        $this->filename = base_path() . '/database/seeds/csvs/timezones.csv';
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
