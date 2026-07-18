<?php

namespace App\Console\Commands;

use App\Mail\OverstayAlertMail;
use App\Models\Attendance;
use App\Models\Library;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class CheckOverstays extends Command
{
    protected $signature = 'attendance:check-overstays';
    protected $description = 'Email library owners when students are still checked in past their shift end time';

    public function handle(): int
    {
        $libraries = Library::whereNotNull('email')->get();
        $emailsSent = 0;

        foreach ($libraries as $library) {
            $overstayed = Attendance::overstayedToday($library->id)
                ->whereNull('overstay_notified_at');

            if ($overstayed->isEmpty()) {
                continue;
            }

            Mail::to($library->email)->send(new OverstayAlertMail($library, $overstayed));

            Attendance::whereIn('id', $overstayed->pluck('id'))
                ->update(['overstay_notified_at' => now()]);

            $emailsSent++;
            $this->info("Sent overstay alert to {$library->email} ({$overstayed->count()} student(s)) for {$library->name}");
        }

        if ($emailsSent === 0) {
            $this->info('No new overstay alerts to send.');
        }

        return self::SUCCESS;
    }
}
