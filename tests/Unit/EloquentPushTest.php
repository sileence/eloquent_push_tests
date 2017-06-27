<?php

namespace Tests\Unit;

use App\Tag;
use App\Post;
use App\Comment;
use App\Category;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class EloquentPushTest extends TestCase
{
    use DatabaseTransactions;

    function test_eloquent_push_can_save_belongs_to_relations_on_existing_models()
    {
        // If the category already exists, it won't throw a constraint error.
        $category = factory(Category::class)->create();

        $post = factory(Post::class)->make();

        $post->category()->associate($category);

        $post->push();

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => $category->name,
        ]);

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'category_id' => $category->id,
        ]);
    }

    /**
     * If the category does not exist, it will throw a constraint error.
     */
    function test_eloquent_push_can_save_belongs_to_relations()
    {
        $category = factory(Category::class)->make();

        $post = factory(Post::class)->make();

        $post->category()->associate($category);

        $post->push();

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => $category->name,
        ]);

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'category_id' => $category->id,
        ]);
    }

    function test_eloquent_push_can_save_has_many_relations()
    {
        $category = factory(Category::class)->create();

        $post = factory(Post::class)->make();

        $post->category()->associate($category);

        $post->comments->add(factory(Comment::class)->make());
        $post->comments->add(factory(Comment::class)->make());
        $post->comments->add(factory(Comment::class)->make());

        $post->push();

        $this->assertDatabaseHas('posts', [
            'title' => $post->title,
        ]);

        $this->assertCount(3, Comment::where('post_id', $post->id)->get());
    }

    function test_eloquent_push_can_save_belongs_to_and_has_many_relations()
    {
        // Just create the model but don't persist it.
        $category = factory(Category::class)->make();

        $post = factory(Post::class)->make();

        $post->category()->associate($category);

        $post->comments->add(factory(Comment::class)->make());
        $post->comments->add(factory(Comment::class)->make());
        $post->comments->add(factory(Comment::class)->make());

        $post->push();

        // push persist the belongs to relation
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => $category->name,
        ]);

        // the model itself
        $this->assertDatabaseHas('posts', [
            'title' => $post->title,
        ]);

        // the has many relation
        $this->assertCount(3, Comment::where('post_id', $post->id)->get());
    }

    function test_eloquent_push_can_save_belongs_to_many_relations()
    {
        $post = factory(Post::class)->create();

        $tags = factory(Tag::class)->times(2)->create();

        $post->tags()->attach($tags);

        $post->tags->first()->name = 'Renamed tag';

        $post->push();

        // Get a fresh post and test the first tag was renamed in the DB.
        $post->refresh();

        $this->assertSame(
            'Renamed tag', $post->tags->first()->name
        );
    }
}
