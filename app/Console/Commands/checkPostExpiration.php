<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\posts\Post;

class checkPostExpiration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-post-expiration';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Post::where('status', 'approved')
            ->where('approved_at', '<=', now()->subMonths(2))
            ->update(['status' => 'expired']);
    }
}
