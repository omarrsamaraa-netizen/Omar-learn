<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Item>
 */
class ItemFactory extends Factory
{
    /**
     * Materials a piece can be made from.
     *
     * @var list<string>
     */
    private const MATERIALS = ['Gold', 'Silver', 'Leather', 'Linen', 'Silk', 'Cashmere', 'Walnut', 'Brass'];

    /**
     * Kinds of piece the store carries.
     *
     * @var list<string>
     */
    private const PIECES = ['Wristwatch', 'Suit', 'Tie', 'Cufflinks', 'Satchel', 'Overcoat', 'Loafers', 'Pocket Square'];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(self::MATERIALS).' '.fake()->randomElement(self::PIECES),
            'caption' => fake()->realText(300),
            'rate' => fake()->numberBetween(0, 100),
            'category_id' => Category::inRandomOrder()->first()->id,
        ];
    }

    /**
     * Indicate that the piece should be highlighted on the collection page.
     */
    public function highlighted(): static
    {
        return $this->state(fn (array $attributes): array => [
            'rate' => fake()->numberBetween(71, 100),
        ]);
    }
}
