<?php

namespace Modules\Client\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Client\Entities\CreditScoreRange;

class CreditScoreRangeSeederTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Model::unguard();

        // $this->call("OthersTableSeeder");
        $ranges = [
            ['min_score' => 401, 'max_score' => 500, 'rating_label' => 'Excellent', 'color_code' => '#22c55e', 'description' => 'Exceptional creditworthiness', 'sort_order' => 1],
            ['min_score' => 301, 'max_score' => 400, 'rating_label' => 'Very Good', 'color_code' => '#84cc16', 'description' => 'Very dependable borrower', 'sort_order' => 2],
            ['min_score' => 201, 'max_score' => 300, 'rating_label' => 'Good', 'color_code' => '#eab308', 'description' => 'Generally reliable borrower', 'sort_order' => 3],
            ['min_score' => 91, 'max_score' => 200, 'rating_label' => 'Fair', 'color_code' => '#f97316', 'description' => 'Below average borrower', 'sort_order' => 4],
            ['min_score' => 0, 'max_score' => 90, 'rating_label' => 'Poor', 'color_code' => '#ef4444', 'description' => 'High risk borrower', 'sort_order' => 5],
        ];

        foreach ($ranges as $range) {
            CreditScoreRange::create($range);
        }
    
    }
}
