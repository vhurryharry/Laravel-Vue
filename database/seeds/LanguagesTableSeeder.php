<?php

// use Illuminate\Database\Seeder;
use Flynsarmy\CsvSeeder\CsvSeeder;

class LanguagesTableSeeder extends CsvSeeder
{
    public function __construct()
    {
        $this->table = 'language_values';
        $this->filename = base_path() . '/database/seeds/csvs/languages.csv';
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
