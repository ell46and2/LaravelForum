<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ParticipateInForumTest extends TestCase
{
	use DatabaseMigrations;

	/** @test */
	function unauthenticated_users_may_not_add_replies()
	{
        $this->withExceptionHandling()
        	->post('/threads/channel-name/1/replies', []) // Need to be authenticated to post to this route.
        	->assertRedirect('/login');
	}

    /** @test */
    public function an_authenticated_user_may_participate_in_forum_threads()
    {
        // Given we have an authenticated user
        $this->signIn(); // sets the currently loged in user.

        // And an existing thread
        $thread = create('App\Thread');

        // When the user adds a reply to the thread
        $reply = make('App\Reply'); // make() just adds to memory not to test DB.
        $this->post($thread->path() . '/replies', $reply->toArray());

        // Then their reply should be visible on the page.
        $this->get($thread->path())
        	->assertSee($reply->body);
    }

    /** @test */
    public function a_reply_requires_a_body()
    {
    	$this->withExceptionHandling()->signIn();

        $thread = create('App\Thread');

        $reply = make('App\Reply', ['body' => null]);

        $this->post($thread->path() . '/replies', $reply->toArray())
        	->assertSessionHasErrors('body');
    }
}
