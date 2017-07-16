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
		$this->expectException('Illuminate\Auth\AuthenticationException'); // No authenticated user, so should cause exception.

        $this->post('/threads/1/replies', []); // Need to be authenticated to post to this route.

	}

    /** @test */
    public function an_authenticated_user_may_participate_in_forum_threads()
    {
        // Given we have an authenticated user
        $user = create('App\User');
        $this->signIn($user); // sets the currently loged in user.

        // And an existing thread
        $thread = create('App\Thread');

        // When the user adds a reply to the thread
        $reply = make('App\Reply'); // make() just adds to memory not to test DB.
        $this->post('/threads/' . $thread->id . '/replies', $reply->toArray());

        // Then their reply should be visible on the page.
        $this->get('/threads/' . $thread->id)
        	->assertSee($reply->body);
    }
}
