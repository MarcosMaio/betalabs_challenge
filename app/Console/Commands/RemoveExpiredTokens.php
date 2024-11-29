<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Laravel\Sanctum\PersonalAccessToken;

class RemoveExpiredTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tokens:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove expired personal access tokens';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $count = PersonalAccessToken::whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->delete();

            $this->info("Deleted {$count} expired tokens.");

        return 0;
    }
}
