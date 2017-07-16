<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CreateThreadsTest extends TestCase
{
	use DatabaseMigrations;

	/** @test */
    public function guests_may_not_create_threads() 
    {
    	$this->expectException('Illuminate\Auth\AuthenticationException'); // No authenticated user, so should cause exception.

    	$thread = make('App\Thread'); 

    	$this->post('/threads', $thread->toArray());
    }

    /** @test */
    public function guests_cannot_see_the_create_thread_page()
    {
    	$this->withExceptionHandling();

    	$this->get('/threads/create')
    		->assertRedirect('/login');
    }

    /** @test */
    public function an_authenticated_user_can_create_new_forum_threads()
    {
    	// Given we have a signed in user
    	$this->signIn();

    	// When we hit the endpoint to create a new thread
    	$thread = make('App\Thread'); 

    	$this->post('/threads', $thread->toArray());

    	// Then, when we visit the thread page.
    	// We should see the new thread
    	$this->get('/threads/' . $thread->id)
			->assertSee($thread->title)
    		->assertSee($thread->body);
    }
}
