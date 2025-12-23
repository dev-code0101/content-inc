<?php

namespace Tests\Feature\Front;

use App\Models\Post;
use App\Models\Category;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\CreatesBasicData;

class HomeControllerTest extends TestCase
{
    use RefreshDatabase, CreatesBasicData;

    public function test_it_returns_published_posts_in_homepage()
    {
        // Set pagination config value to 10 for testing purposes
        config(['app.num_items_per_page' => 10]);

        // Arrange
        $basicData = $this->createBasicData();
        $user = $basicData['user'];
        $category = $basicData['category'];

        // Published post (must be visible)
        $publishedPost = Post::factory()->create([
            'user_id'     => $user->id,
            'category_id' => $category->id,
            'status'      => true, // مطابق للـ scope published()
        ]);

        // Draft post (must NOT be visible)
        $draftPost = Post::factory()->create([
            'user_id'     => $user->id,
            'category_id' => $category->id,
            'status'      => false,
        ]);

        // Act
        $response = $this->get('/');

        // Assert
        $response->assertStatus(200)
            ->assertViewIs('front.index')
            ->assertViewHas('posts');

        $posts = $response->viewData('posts');

        // Assert only published posts appear
        $this->assertTrue($posts->contains($publishedPost));
        $this->assertFalse($posts->contains($draftPost));

        // Category & User relations must be eager loaded
        $this->assertTrue($posts->first()->relationLoaded('category'));
        $this->assertTrue($posts->first()->relationLoaded('user'));
    }

    public function test_it_paginates_posts_correctly()
    {
        // Set pagination config value to 10 for testing purposes
        config(['app.num_items_per_page' => 10]);

        // Arrange
        $basicData = $this->createBasicData();
        $user = $basicData['user'];
        $category = $basicData['category'];

        // Create 30 published posts with existing user and category
        Post::factory()->count(30)->create([
            'user_id'     => $user->id,
            'category_id' => $category->id,
            'status'      => true, // published
        ]);

        // Act
        $response = $this->get('/');

        // Assert
        $response->assertStatus(200);

        /** @var \Illuminate\Contracts\Pagination\LengthAwarePaginator $posts */
        $posts = $response->viewData('posts');

        // Assert pagination matches app config
        $this->assertEquals(
            config('app.num_items_per_page'),
            $posts->perPage()
        );
    }
}
