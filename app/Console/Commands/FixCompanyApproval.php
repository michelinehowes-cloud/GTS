<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Company;

class FixCompanyApproval extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:company-approval';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set is_approved to true for all companies.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $updatedCount = Company::query()->update(['is_approved' => true]);
        $this->info("Updated {$updatedCount} companies to be approved.");
        
        $approvedCount = Company::where('is_approved', true)->count();
        $this->info("Total approved companies: {$approvedCount}");

        return Command::SUCCESS;
    }
}
