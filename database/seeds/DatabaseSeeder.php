<?php

use Illuminate\Database\Seeder;
use App\User;
use App\Group;
use App\Version;
use App\Post;
use App\Comment;
use App\CodeSnippet;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UsersTableSeeder::class);
        $this->call(GroupsTableSeeder::class);
        $this->call(VersionsTableSeeder::class);
        $this->call(PostsTableSeeder::class);
        $this->call(CommentsTableSeeder::class);
        $this->call(CodeSnippetsTableSeeder::class);
        $this->call(KeywordsTableSeeder::class);
    }
}
