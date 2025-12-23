<?php

namespace Tests\Feature\Front;

use App\Models\Post;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\CreatesBasicData;

class TagControllerTest extends TestCase
{
    use RefreshDatabase, CreatesBasicData;

    public function test_it_returns_published_posts_for_given_tag()
    {
        config(['app.num_items_per_page' => 10]);

        // Arrange
        $basicData = $this->createBasicData();
        $user      = $basicData['user'];
        $category  = $basicData['category'];

        // Create tags
        $tag = Tag::factory()->create([
            'name' => 'laravel',
        ]);

        $anotherTag = Tag::factory()->create([
            'name' => 'php',
        ]);

        // Published post related to the "laravel" tag (must be visible)
        $publishedPostWithTag = Post::factory()->create([
            'user_id'     => $user->id,
            'category_id' => $category->id,
            'status'      => true,
        ]);
        $publishedPostWithTag->tags()->attach($tag->id);

        // Draft post related to the tag (must not be visible)
        $draftPostWithTag = Post::factory()->create([
            'user_id'     => $user->id,
            'category_id' => $category->id,
            'status'      => false,
        ]);
        $draftPostWithTag->tags()->attach($tag->id);

        // Published post with another tag (must not be visible)
        $postWithAnotherTag = Post::factory()->create([
            'user_id'     => $user->id,
            'category_id' => $category->id,
            'status'      => true,
        ]);
        $postWithAnotherTag->tags()->attach($anotherTag->id);

        // Act
        $response = $this->get(route('tag.show', $tag->name));

        // Assert
        $response->assertStatus(200)
            ->assertViewIs('front.tag')
            ->assertViewHasAll(['posts', 'tag']);

        /** @var \Illuminate\Contracts\Pagination\LengthAwarePaginator $paginator */
        $paginator   = $response->viewData('posts');
        $tagFromView = $response->viewData('tag');

        // The tag in the view must match the requested tag name
        $this->assertEquals('laravel', $tagFromView);

        /** @var \Illuminate\Support\Collection $posts */
        $posts = $paginator->getCollection()->collect();

        // The paginator must contain the published post with the same tag
        $this->assertTrue(
            $posts->contains(fn($p) => $p->id === $publishedPostWithTag->id)
        );

        // It must not contain the draft post nor the post with another tag
        $this->assertFalse(
            $posts->contains(fn($p) => $p->id === $draftPostWithTag->id)
        );

        $this->assertFalse(
            $posts->contains(fn($p) => $p->id === $postWithAnotherTag->id)
        );

        // Assert that all returned posts are published
        $this->assertTrue(
            $posts->every(fn($post) => $post->status === true)
        );

        // Ensure pagination is correct
        $this->assertEquals(
            config('app.num_items_per_page'),
            $paginator->perPage()
        );
    }

    public function test_it_returns_404_if_tag_not_found()
    {
        $response = $this->get(route('tag.show', 'unknown-tag'));

        $response->assertStatus(404);
    }

    public function test_it_paginates_posts_for_tag_correctly()
    {
        config(['app.num_items_per_page' => 10]);

        // Arrange
        $basicData = $this->createBasicData();
        $user      = $basicData['user'];
        $category  = $basicData['category'];

        $tag = Tag::factory()->create([
            'name' => 'laravel',
        ]);

        // 30 posts related to the tag
        Post::factory()->count(30)->create([
            'user_id'     => $user->id,
            'category_id' => $category->id,
            'status'      => true,
        ])->each(function (Post $post) use ($tag) {
            $post->tags()->attach($tag->id);
        });

        // Act
        $response = $this->get(route('tag.show', $tag->name));

        // Assert
        $response->assertStatus(200);

        /** @var \Illuminate\Contracts\Pagination\LengthAwarePaginator $posts */
        $posts = $response->viewData('posts');

        $this->assertEquals(
            config('app.num_items_per_page'),
            $posts->perPage()
        );
    }
}
