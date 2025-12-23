<?php

namespace Tests\Feature\Front;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cookie;
use Tests\TestCase;
use Tests\Traits\CreatesBasicData;

class PostControllerTest extends TestCase
{
    use RefreshDatabase, CreatesBasicData;

    public function test_it_returns_post_by_slug()
    {
        // Arrange
        $basicData = $this->createBasicData();
        $user      = $basicData['user'];
        $category  = $basicData['category'];

        $post = Post::factory()->create([
            'title'       => 'My First Post',
            'slug'        => 'my-first-post',
            'content'     => 'This is the content of the post.',
            'image'       => 'post.jpg',
            'status'      => true,
            'views'       => 15,
            'category_id' => $category->id,
            'user_id'     => $user->id,
        ]);

        // Act
        $response = $this->get(route('post.show', $post->slug));

        // Assert
        $response->assertStatus(200)
            ->assertViewIs('front.post')
            ->assertViewHas('post');

        $returnedPost = $response->viewData('post');

        $this->assertEquals($post->id,        $returnedPost->id);
        $this->assertEquals('my-first-post',  $returnedPost->slug);
        $this->assertEquals('My First Post',  $returnedPost->title);
        $this->assertEquals('This is the content of the post.', $returnedPost->content);
        $this->assertEquals('post.jpg',       $returnedPost->getRawOriginal('image'));
        $this->assertEquals(true,             $returnedPost->status);
        $this->assertEquals(16,               $returnedPost->views); // views + 1
        $this->assertEquals($category->id,    $returnedPost->category_id);
        $this->assertEquals($user->id,        $returnedPost->user_id);
    }

    public function test_it_increments_post_views_on_first_visit()
    {
        // Arrange
        $basicData = $this->createBasicData();
        $user      = $basicData['user'];
        $category  = $basicData['category'];

        $post = Post::factory()->create([
            'title'       => 'Test Post',
            'slug'        => 'test-post',
            'status'      => true,
            'views'       => 0,
            'user_id'     => $user->id,
            'category_id' => $category->id,
        ]);

        // Act
        $response = $this->get(route('post.show', $post->slug));

        // Assert
        $response->assertStatus(200);

        $post->refresh();
        $this->assertEquals(1, $post->views); // views +1
        
        $response->assertCookie('post_viewed_' . $post->id);
    }


    public function test_it_does_not_increment_views_if_cookie_exists()
    {
        // Arrange
        $basicData = $this->createBasicData();
        $user      = $basicData['user'];
        $category  = $basicData['category'];

        $post = Post::factory()->create([
            'title'       => 'Test Post',
            'slug'        => 'test-post',
            'status'      => true,
            'views'       => 5,
            'user_id'     => $user->id,
            'category_id' => $category->id,
        ]);

        // Create the cookie manually before the visit
        Cookie::queue('post_viewed_' . $post->id, true);

        // Act
        $response = $this->withCookie('post_viewed_' . $post->id, true)
            ->get(route('post.show', $post->slug));

        // Assert
        $response->assertStatus(200);

        $post->refresh();
        $this->assertEquals(5, $post->views); // doesn't increased
    }

    public function test_it_increments_views_again_after_cookie_is_removed()
    {
        // Arrange
        $basicData = $this->createBasicData();
        $user      = $basicData['user'];
        $category  = $basicData['category'];

        $post = Post::factory()->create([
            'title'       => 'Test Post',
            'slug'        => 'test-post',
            'status'      => true,
            'views'       => 10,
            'user_id'     => $user->id,
            'category_id' => $category->id,
        ]);

        // Visit without cookie (first)
        $this->get(route('post.show', $post->slug));
        $post->refresh();

        $this->assertEquals(11, $post->views);

        // Remove cookie
        Cookie::queue(Cookie::forget('post_viewed_' . $post->id));

        // Visit again → should increment
        $this->get(route('post.show', $post->slug));
        $post->refresh();

        $this->assertEquals(12, $post->views);
    }


    public function test_it_returns_404_if_post_not_found()
    {
        $response = $this->get(route('post.show', 'non-existing-post'));

        $response->assertStatus(404);
    }
}
