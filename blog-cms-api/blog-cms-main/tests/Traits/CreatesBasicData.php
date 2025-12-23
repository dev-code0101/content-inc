<?php

namespace Tests\Traits;

use App\Models\Category;
use App\Models\Setting;
use App\Models\User;

trait CreatesBasicData
{
    protected function createBasicData()
    {
        // Settings (لو بتحتاجها دائمًا)
        Setting::factory()->create([
            'site_name' => fake()->word(),
            'contact_email' => fake()->email(),
            'description' => fake()->sentence(),
            'about' => fake()->paragraph(),
            'copy_rights' => fake()->sentence(),
            'url_fb' => fake()->url(),
            'url_twitter' => fake()->url(),
            'url_insta' => fake()->url(),
            'url_linkedin' => fake()->url(),
        ]);

        // Create user
        $user = User::factory()->create();

        // Create category
        $category = Category::factory()->create([
            'name'    => 'Tech',
            'slug'    => 'tech',
            'user_id' => $user->id,
        ]);

        return [
            'user'     => $user,
            'category' => $category,
        ];
    }
}
