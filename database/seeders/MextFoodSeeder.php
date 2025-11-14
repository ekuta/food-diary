<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MextFoodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $filePath = database_path('seeders/20230428-mxt_kagsei-mext_00001_012.csv');
        if (($fp = fopen($filePath, 'r')) === false) {
            throw new \Exception("エクセルファイルのオープンに失敗しました($filePath)。");
        }
        $header = fgetcsv($fp);
        while (($rows = fgetcsv($fp)) !== false) {
            $rows = array_map(function($row) {
                $row = str_replace('(', '', $row);
                $row = str_replace(')', '', $row);
                $row = str_replace('"', '', $row);
                if (in_array($row, ['Tr', '-'], true)) {
                    return 0;
                }
                return $row;
            }, $rows);
            $data = array_combine($header, $rows);
            if ($data['NACL_EQ'] == 'General') {
                print("SKIP " . $data['成分識別子'] . "\n");
                continue;
            }
            DB::table('mext_food')->insert([
                'name'  => $data['成分識別子'],
                'calory' => $data['ENERC_KCAL'],
                'protein'   => $data['PROT-'],
                'fat'   => $data['FAT-'],
                'carbs'   => $data['CHOCDF-'],
                'salt'   => $data['NACL_EQ'],
                'memo'   => $data['MEMO'],
            ]);
        }
        fclose($fp);
    }
}
