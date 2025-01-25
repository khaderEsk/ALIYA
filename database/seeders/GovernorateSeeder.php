<?php

namespace Database\Seeders;

use App\Models\Governorate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GovernorateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Governorate::create([
            'name' => 'دمشق',
        ]);
        Governorate::create([
            'name' => 'ريف دمشق',
        ]);
        Governorate::create([
            'name' => 'حمص',
        ]);
        Governorate::create([
            'name' => 'حماه',
        ]);
        Governorate::create([
            'name' => 'طرطوس',
        ]);
        Governorate::create([
            'name' => 'اللاذقية',
        ]);
        Governorate::create([
            'name' => 'حلب',
        ]);
        Governorate::create([
            'name' => 'ادلب',
        ]);
        Governorate::create([
            'name' => 'ديرالزور',
        ]);
        Governorate::create([
            'name' => 'الحسكة',
        ]);
        Governorate::create([
            'name' => 'درعا',
        ]);
        Governorate::create([
            'name' => 'السويداء',
        ]);
        Governorate::create([
            'name' => 'الرقة',
        ]);
        Governorate::create([
            'name' => 'القنيطرة',
        ]);
    }
}
