<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Http;

class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $http = Http::get('https://loremflickr.com/320/240?random=1')->effectiveUri();
        $url = $http->getScheme()."://". $http->getHost()."/". $http->getPath();
        return [
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->words(6, true),
            'image_url' => $url,
            'price' => rand(10, 250)
        ];
    }
}
