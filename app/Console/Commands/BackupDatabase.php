<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use Illuminate\Console\Command;

class BackupDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:backup {--database= : The database connection to backup}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup the database daily and send it by email';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $database = $this->option('database');
        $date = date("Y-m-d-H-i-s");
        $filename = "backup-$date.sql.gz";

        Artisan::call("db:backup", [
            "--database" => $database,
            "--destination" => "local",
            "--destinationPath" => $filename,
            "--compression" => "gzip"
        ]);

        $file_path = storage_path("app/backups/$filename");
        $mail_to = 'youremail@example.com';
        $mail_subject = 'Daily database backup';
        $mail_body = 'Please find the daily database backup attached to this email';

        Mail::send(['text' => 'mail'], [], function ($message) use ($file_path, $mail_to, $mail_subject, $mail_body) {
            $message->to($mail_to)
                ->subject($mail_subject)
                ->attach($file_path)
                ->setBody($mail_body);
        });

        $this->info("The backup was successful and sent to $mail_to");
    }
}
