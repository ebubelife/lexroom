<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;

class ClearRoomsAndFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clear-rooms-and-files {--force : Force the operation to run without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all rooms, cases, and evidence files from the database and storage';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->option('force') && !$this->confirm('This will delete ALL rooms, cases, messages, reports, and evidence files. Are you sure you want to proceed?')) {
            $this->info('Operation cancelled.');
            return;
        }

        $this->info('Clearing database tables...');

        try {
            Schema::disableForeignKeyConstraints();

            DB::table('commissions')->truncate();
            DB::table('reports')->truncate();
            DB::table('session_messages')->truncate();
            DB::table('evidence_files')->truncate();
            DB::table('rooms')->truncate();

            Schema::enableForeignKeyConstraints();

            $this->info('Database tables cleared successfully.');
        } catch (\Exception $e) {
            $this->error('Error clearing database: ' . $e->getMessage());
            Schema::enableForeignKeyConstraints();
            return;
        }

        $this->info('Clearing storage files...');

        try {
            // Clear evidence files in storage/app/private/evidence/
            if (Storage::disk('local')->exists('evidence')) {
                $directories = Storage::disk('local')->directories('evidence');
                foreach ($directories as $directory) {
                    Storage::disk('local')->deleteDirectory($directory);
                }
                $this->info('Storage directory "evidence" cleared.');
            } else {
                $this->info('No evidence storage directory found.');
            }
        } catch (\Exception $e) {
            $this->error('Error clearing storage: ' . $e->getMessage());
        }

        $this->info('Cleanup completed successfully!');
    }
}
