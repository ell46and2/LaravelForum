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
    	$this->withExceptionHandling();
    	
    	// Can't post	
    	$this->post('/threads')
    		->assertRedirect('/login');

    	// Can't see create page
    	$this->get('/threads/create')
    		->assertRedirect('/login');

    	// $this->expectException('Illuminate\Auth\AuthenticationException'); // No authenticated user, so should cause exception.

    	// $thread = make('App\Thread'); 

    	// $this->post('/threads', $thread->toArray());
    }


    /** @test */
    public function an_authenticated_user_can_create_new_forum_threads()
    {
    	// Given we have a signed in user
    	$this->signIn();

    	// make a thread
    	$thread = make('App\Thread'); 

    	// post the thread values
    	$response = $this->post('/threads', $thread->toArray());

    	// Then, when we visit the thread page.
    	// We should see the new thread
    	$this->get($response->headers->get('location')) // $response->headers->get('location') - gets the uri after post is created.
			->assertSee($thread->title)
    		->assertSee($thread->body);
    }

    // helper function
	public function publishThread($overrides = [])
    {
    	$this->withExceptionHandling()->signIn();

    	$thread = make('App\Thread', $overrides);

    	return $this->post('/threads', $thread->toArray());
    }


    /** @test */
    public function a_thread_requires_a_title()
    {
    	$this->publishThread(['title' => null])
    		->assertSessionHasErrors('title');
    }


    /** @test */
    public function a_thread_requires_a_body()
    {
    	$this->publishThread(['body' => null])
    		->assertSessionHasErrors('body');
    }

    /** @test */
    public function a_thread_requires_a_valid_channel()
    {
    	factory('App\Channel', 2)->create();

    	$this->publishThread(['channel_id' => null])
    		->assertSessionHasErrors('channel_id');

    	// a invalid channel_id
    	$this->publishThread(['channel_id' => 9999])
    		->assertSessionHasErrors('channel_id');
    }

    
}
