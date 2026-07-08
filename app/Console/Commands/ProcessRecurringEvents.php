<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\CustomField\Entities\CustomFieldMeta;
use Modules\Expense\Entities\Expense;
use Modules\Payroll\Entities\Payroll;

class ProcessRecurringEvents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     * This Laravel Artisan command (events:recur) automates the creation of r
     * ecurring expenses and payroll entries.
     * For Expenses:
     * It checks for all expenses marked as recurring (recurring = 1).
     * If today's date matches their recur_next_date (and they haven't passed an optional end date), it duplicates the expense record for today, copies its custom fields, and updates its recur_next_date to the next scheduled date.
     * For Payroll:
     * It does the same for payroll records—clones the payroll entry, copies all payroll meta data, and updates recur_next_date.
     * This prevents manual re-entry of periodic transactions like rent, utility bills, or monthly salaries.
     */
    protected $signature = 'events:recur';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process Recurring events';

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
        // Process recurring expenses
        $this->processRecurring(
            Expense::where('recurring', 1)->get(),
            function ($original) {
                $copy = new Expense();
                $copy->expense_type_id = $original->expense_type_id;
                $copy->amount = $original->amount;
                $copy->notes = $original->notes;
                $copy->date = date("Y-m-d");
                [$year, $month] = explode('-', date("Y-m-d"));
                $copy->year = $year;
                $copy->month = $month;
                $copy->save();

                // Copy custom fields
                $custom_fields = CustomFieldMeta::where('parent_id', $original->id)
                    ->where('category', 'expenses')
                    ->get();

                foreach ($custom_fields as $key) {
                    CustomFieldMeta::create([
                        'name' => $key->name,
                        'parent_id' => $copy->id,
                        'custom_field_id' => $key->custom_field_id,
                        'category' => "expenses",
                    ]);
                }
            }
        );

        // Process recurring payroll
        $this->processRecurring(
            Payroll::where('recurring', 1)->get(),
            function ($original) {
                $copy = new Payroll();
                $copy->payroll_template_id = $original->payroll_template_id;
                $copy->user_id = $original->user_id;
                $copy->employee_name = $original->employee_name;
                $copy->business_name = $original->business_name;
                $copy->payment_method = $original->payment_method;
                $copy->bank_name = $original->bank_name;
                $copy->account_number = $original->account_number;
                $copy->description = $original->description;
                $copy->comments = $original->comments;
                $copy->paid_amount = $original->paid_amount;
                [$year, $month] = explode('-', date("Y-m-d"));
                $copy->date = date("Y-m-d");
                $copy->year = $year;
                $copy->month = $month;
                $copy->save();

                // Copy payroll meta
                $metas = PayrollMeta::where('payroll_id', $original->id)->get();
                foreach ($metas as $key) {
                    PayrollMeta::create([
                        'value' => $key->value,
                        'payroll_id' => $copy->id,
                        'payroll_template_meta_id' => $key->payroll_template_meta_id,
                        'position' => $key->position,
                    ]);
                }
            }
        );
    }

    /**
     * Generic recurring handler
     */
    private function processRecurring($items, callable $duplicateCallback)
    {
        foreach ($items as $item) {
            $shouldProcess = empty($item->recur_end_date)
                ? $item->recur_next_date == date("Y-m-d")
                : (date("Y-m-d") <= $item->recur_end_date && $item->recur_next_date == date("Y-m-d"));

            if ($shouldProcess) {
                // Duplicate record
                $duplicateCallback($item);

                // Update next recurrence date
                $item->recur_next_date = date_format(
                    date_add(date_create(date("Y-m-d")),
                        date_interval_create_from_date_string($item->recur_frequency . ' ' . $item->recur_type . 's')
                    ),
                    'Y-m-d'
                );
                $item->save();
            }
        }
    }
}