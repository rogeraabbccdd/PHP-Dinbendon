<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;

class GenerateUserInsertCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:generate-insert {--file= : Path to the file with user data, one per line.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates multiple SQL INSERT statements from a data file.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filePath = $this->option('file');

        if (!$filePath) {
            $this->error('Please provide a file path using the --file option.');
            return 1;
        }

        if (!File::exists($filePath)) {
            $this->error("File not found at: {$filePath}");
            return 1;
        }

        $lines = File::lines($filePath);

        $this->info("-- Generating SQL for " . $lines->count() . " user(s) at " . Carbon::now()->toDateTimeString());

        foreach ($lines as $line) {
            $this->generateSqlForLine($line);
        }

        $this->info("-- SQL generation complete. --");
        return 0;
    }

    /**
     * Generates and outputs the SQL for a single line of user data.
     *
     * @param string $line
     */
    private function generateSqlForLine(string $line)
    {
        $userData = array_map('trim', explode(',', $line));

        if (count($userData) !== 4) {
            $this->warn("Skipping invalid line (expected 4 fields): {$line}");
            return;
        }

        [$student_id, $name, $course_id, $seat_number] = $userData;

        $password = Hash::make($student_id);

        $sql = sprintf(
            "INSERT INTO users (student_id, name, password, course_id, seat_number, enabled, created_at, updated_at) VALUES ('%s', '%s', '%s', %d, %d, true, now(), now());",
            $student_id,
            $name,
            $password,
            $course_id,
            $seat_number,
        );

        $this->line($sql);
    }
}
