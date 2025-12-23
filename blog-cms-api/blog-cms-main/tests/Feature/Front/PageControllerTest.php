<?php

namespace Tests\Feature\Front;

use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\CreatesBasicData;

class PageControllerTest extends TestCase
{
    use RefreshDatabase, CreatesBasicData;

    public function test_it_returns_page_by_slug()
    {
        // Arrange
        $basicData = $this->createBasicData();
        $user      = $basicData['user'];

        $page = Page::factory()->create([
            'name'    => 'About Us',
            'slug'    => 'about-us',
            'content' => 'This is the about page content.',
            'navbar'  => true,
            'footer'  => false,
            'user_id' => $user->id,
        ]);

        // Act
        $response = $this->get(route('page.show', $page->slug));

        // Assert
        $response->assertStatus(200)
            ->assertViewIs('front.page')
            ->assertViewHas('page');

        $returnedPage = $response->viewData('page');

        $this->assertEquals($page->id, $returnedPage->id);
        $this->assertEquals('about-us', $returnedPage->slug);
        $this->assertEquals('About Us', $returnedPage->name);
        $this->assertEquals('This is the about page content.', $returnedPage->content);
    }

    public function test_it_returns_404_if_page_not_found()
    {
        // Act
        $response = $this->get(route('page.show', 'non-existing-page'));

        // Assert
        $response->assertStatus(404);
    }
}
