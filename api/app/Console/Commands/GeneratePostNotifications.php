<?php

namespace App\Console\Commands;

use App\Jobs\SendNewPostNotification;
use App\Models\Post;
use Illuminate\Console\Command;

class GeneratePostNotifications extends Command
{
    protected $signature = 'generate:notifications {post_id} {count} {mode?}';
    protected $description = 'Generate notification jobs for a post';

    public function handle()
    {
        $postId = $this->argument('post_id');
        $count = $this->argument('count');
        $mode = $this->argument('mode');
        
        $post = Post::findOrFail($postId);
        
        for ($i = 0; $i < $count; $i++) {
            if($mode === 'normal'){
                SendNewPostNotification::dispatch($post);
            } else {
                SendNewPostNotification::dispatch($post)->onQueue('high');
            }

        }

        $this->info("Generated $count notification jobs for post ID $postId.");
    }
}