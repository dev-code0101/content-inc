<?php

namespace Tests\Feature\Front;

use App\Models\Post;
use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\CreatesBasicData;

class CommentControllerTest extends TestCase
{
    use RefreshDatabase, CreatesBasicData;

    public function test_authenticated_user_can_add_comment_to_post()
    {
        // Arrange
        $basicData = $this->createBasicData();
        $user      = $basicData['user'];
        $category  = $basicData['category'];

        $post = Post::factory()->create([
            'user_id'     => $user->id,
            'category_id' => $category->id,
            'status'      => true,
        ]);

        $this->actingAs($user);

        $payload = [
            'body' => 'This is a test comment.',
        ];

        // Act
        $response = $this->post(
            route('post.comment', $post->id),
            $payload
        );

        // Assert
        $response->assertStatus(302);

        $this->assertDatabaseHas('comments', [
            'body'             => 'This is a test comment.',
            'user_id'          => $user->id,
            'commentable_id'   => $post->id,
            'commentable_type' => Post::class,
        ]);
    }

    public function test_guest_cannot_add_comment_to_post()
    {
        // Arrange
        $basicData = $this->createBasicData();
        $user      = $basicData['user'];
        $category  = $basicData['category'];

        $post = Post::factory()->create([
            'user_id'     => $user->id,
            'category_id' => $category->id,
            'status'      => true,
        ]);

        $payload = [
            'body' => 'Guest attempt comment.',
        ];

        // Act
        $response = $this->post(route('post.comment', $post->id), $payload);

        // Assert
        $response->assertRedirect(route('login'));

        // Ensure the comment was not saved
        $this->assertDatabaseMissing('comments', [
            'body'             => 'Guest attempt comment.',
            'commentable_id'   => $post->id,
            'commentable_type' => Post::class,
        ]);
    }


    public function test_comment_is_polymorphically_linked_to_post()
    {
        // Arrange
        $basicData = $this->createBasicData();
        $user      = $basicData['user'];
        $category  = $basicData['category'];

        $post = Post::factory()->create([
            'user_id'     => $user->id,
            'category_id' => $category->id,
            'status'      => true,
        ]);

        $this->actingAs($user);

        $payload = [
            'body' => 'Polymorphic relation comment.',
        ];

        // Act
        $this->post(route('post.comment', $post->id), $payload);

        // Assert
        $comment = Comment::first();

        $this->assertNotNull($comment);
        $this->assertInstanceOf(Post::class, $comment->commentable);
        $this->assertTrue($comment->commentable->is($post));
        $this->assertEquals($user->id, $comment->user_id);
    }
}
