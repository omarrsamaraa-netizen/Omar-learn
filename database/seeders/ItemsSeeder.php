<?php

namespace Database\Seeders;

use App\Models\Item;
use Illuminate\Database\Seeder;

class ItemsSeeder extends Seeder
{
    /**
     * Seed the items table with random records.
     */
    public function run(): void
    {
        Item::factory()->count(20)->create();
    }
}
