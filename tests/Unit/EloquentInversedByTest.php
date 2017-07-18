<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\{
    Comment, Post, User, UserProfile
};
use Tests\DetectRepeatedQueries;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class EloquentInversedByTest extends TestCase
{
    use DatabaseTransactions, DetectRepeatedQueries;
    
    function test_has_many_inverse_relationship()
    {
        $post = factory(Post::class)->create();

        factory(Comment::class)->times(2)->create([
            'post_id' => $post->id,
        ]);

        $this->enableQueryLog();

        $this->assertSame($post, $post->comments->first()->post);
        $this->assertSame($post, $post->comments->last()->post);

        $this->assertNotRepeatedQueries()->flushQueryLog();
    }

    function test_has_many_count()
    {
        $post = factory(Post::class)->create();

        factory(Comment::class)->times(2)->create([
            'post_id' => $post->id,
        ]);

        $post = Post::with('comments')->first();

        $this->assertSame(2, $post->comments->count());
    }

    function test_has_many_inverse_relationship_eager_loaded()
    {
        $post = factory(Post::class)->create();

        factory(Comment::class)->create([
            'post_id' => $post->id,
        ]);

        $this->enableQueryLog();

        $post = Post::with('comments')->first();

        $this->assertSame($post, $post->comments->first()->post);

        $this->assertNotRepeatedQueries()->flushQueryLog();
    }

    function test_has_many_inverse_relationship_through_query()
    {
        $post = factory(Post::class)->create();

        factory(Comment::class)->create([
            'post_id' => $post->id,
        ]);

        $this->enableQueryLog();

        $this->assertSame($post, $post->comments()->first()->post);

        $this->assertNotRepeatedQueries()->flushQueryLog();
    }

    function test_has_many_inverse_relationship_when_creating_a_new_model()
    {
        $post = factory(Post::class)->create();

        $comment = $post->comments()->create([
            'comment' => 'New comment'
        ]);

        $this->enableQueryLog();

        $this->assertSame($post, $comment->post);

        $this->assertNotRepeatedQueries()->flushQueryLog();
    }

    function test_has_many_inverse_relationship_when_making_a_new_model()
    {
        $post = factory(Post::class)->create();

        $comment = $post->comments()->make([
            'comment' => 'New comment'
        ]);

        $this->enableQueryLog();

        $this->assertSame($post, $comment->post);

        $this->assertNotRepeatedQueries()->flushQueryLog();
    }

    function test_has_one_inverse_relationship()
    {
        factory(UserProfile::class)->create();

        $this->enableQueryLog();

        $user = User::first();

        $this->assertSame($user, $user->profile->user);

        $this->assertNotRepeatedQueries()->flushQueryLog();
    }

    function test_has_one_inverse_relationship_with_default()
    {
        factory(User::class)->create();

        $this->enableQueryLog();

        $user = User::first();

        $this->assertSame($user, $user->profile->user);

        $this->assertNotRepeatedQueries()->flushQueryLog();
    }

    function test_has_one_inverse_relationship_eager_loaded_with_default()
    {
        factory(User::class)->create();

        $this->enableQueryLog();

        $user = User::with('profile')->first();

        $this->assertSame($user, $user->profile->user);

        $this->assertNotRepeatedQueries()->flushQueryLog();
    }

    function test_has_one_inverse_relationship_when_creating_a_new_model()
    {
        factory(User::class)->create();

        $this->enableQueryLog();

        $user = User::first();

        $profile = $user->profile()->create([
            'profile' => 'Web developer',
        ]);

        $this->assertSame($user, $profile->user);

        $this->assertNotRepeatedQueries()->flushQueryLog();
    }

    function test_morph_one_inverse_relationship()
    {
        $this->enableQueryLog();

        $post = factory(Post::class)->create();

        $post->featuredImage()->create([
            'image' => 'image.jpg',
        ]);

        $this->assertSame($post, $post->featuredImage->featured);

        $this->assertNotRepeatedQueries()->flushQueryLog();
    }

    function test_morph_one_inverse_relationship_with_default()
    {
        $this->enableQueryLog();

        $post = factory(Post::class)->create();

        $post->featuredImage()->create([
            'image' => 'image.jpg',
        ]);

        $this->assertSame($post, $post->featuredImage->featured);

        $this->assertNotRepeatedQueries()->flushQueryLog();
    }

    function test_morph_one_inverse_relationship_eager_loaded_with_default()
    {
        $this->enableQueryLog();

        $post = factory(Post::class)->create();

        $post->featuredImage()->create([
            'image' => 'image.jpg',
        ]);

        $this->assertSame($post, $post->featuredImage->featured);

        $this->assertNotRepeatedQueries()->flushQueryLog();
    }

    function test_morph_many_inverse_relationship()
    {
        $this->enableQueryLog();

        $post = factory(Post::class)->create();

        $image = $post->images()->create([
            'image' => 'image.jpg',
        ]);

        $this->assertSame($post, $image->gallery);

        $this->assertSame($post, $post->images->first()->gallery);

        $this->assertNotRepeatedQueries()->flushQueryLog();
    }

    function test_morph_many_inverse_relationship_with_default()
    {
        $this->enableQueryLog();

        $post = factory(Post::class)->create();

        $post->images()->create([
            'image' => 'image.jpg',
        ]);

        $this->assertSame($post, $post->images->first()->gallery);

        $this->assertNotRepeatedQueries()->flushQueryLog();
    }
}
