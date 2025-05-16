<?php

namespace App\Console\Commands;

use App\Models\Salary;
use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateMonthlySalaries extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-monthly-salaries';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate monthly salary records for all workers at the beginning of each month';

    /**
     * Execute the console command.
     */


    public function handle()
    {
        $today = Carbon::today();

        $workers = Worker::where('salary' ,'!=', 0)->get();

        foreach ($workers as $worker) {
            $existing = Salary::where('worker_id', $worker->id)
                ->whereMonth('created_at', $today->month)
                ->whereYear('created_at', $today->year)
                ->exists();

            if ($existing) {
                $this->info("Salary already exists for worker ID {$worker->id} for this month.");
                continue;
            }
            Salary::create([
                'worker_id' => $worker->id,
                'salary' => $worker->salary ?? 0,
                'payment_status' => 'pending',
            ]);

            $this->info("Salary record created for worker ID {$worker->id}.");
        }

        $this->info('Monthly salary generation complete.');
        return 0;
    }
}
