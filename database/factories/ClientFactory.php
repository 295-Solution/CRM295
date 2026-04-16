<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Client>
 */
class ClientFactory extends Factory
{
    protected $model = Client::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama' => fake()->name(),
            'perusahaan' => fake()->company(),
            'nomor_wa' => '08'.fake()->numerify('##########'),
            'sumber_client' => fake()->randomElement(Client::SOURCE_OPTIONS),
            'jenis_bisnis' => fake()->randomElement(Client::BUSINESS_TYPE_OPTIONS),
        ];
    }
}
