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
    // public function handle()
    // {
    //     //check for recurring expenses
    //     $expenses = Expense::where('recurring', 1)->get();
    //     foreach ($expenses as $expense) {
    //         if (empty($expense->recur_end_date)) {
    //             if ($expense->recur_next_date == date("Y-m-d")) {
    //                 $exp1 = new Expense();
    //                 $exp1->expense_type_id = $expense->expense_type_id;
    //                 $exp1->amount = $expense->amount;
    //                 $exp1->notes = $expense->notes;
    //                 $exp1->date = date("Y-m-d");
    //                 $date = explode('-', date("Y-m-d"));
    //                 $exp1->year = $date[0];
    //                 $exp1->month = $date[1];
    //                 $exp1->save();
    //                 $custom_fields = CustomFieldMeta::where('parent_id', $expense->id)->where('category',
    //                     'expenses')->get();
    //                 foreach ($custom_fields as $key) {
    //                     $custom_field = new CustomFieldMeta();
    //                     $custom_field->name = $key->name;
    //                     $custom_field->parent_id = $exp1->id;
    //                     $custom_field->custom_field_id = $key->custom_field_id;
    //                     $custom_field->category = "expenses";
    //                     $custom_field->save();
    //                 }
    //                 $exp2 = Expense::find($expense->id);
    //                 $exp2->recur_next_date = date_format(date_add(date_create(date("Y-m-d")),
    //                     date_interval_create_from_date_string($expense->recur_frequency . ' ' . $expense->recur_type . 's')),
    //                     'Y-m-d');
    //                 $exp2->save();
    //             }
    //         } else {
    //             if (date("Y-m-d") <= $expense->recur_end_date) {
    //                 if ($expense->recur_next_date == date("Y-m-d")) {
    //                     $exp1 = new Expense();
    //                     $exp1->expense_type_id = $expense->expense_type_id;
    //                     $exp1->amount = $expense->amount;
    //                     $exp1->notes = $expense->notes;
    //                     $exp1->date = date("Y-m-d");
    //                     $date = explode('-', date("Y-m-d"));
    //                     $exp1->year = $date[0];
    //                     $exp1->month = $date[1];
    //                     $exp1->save();
    //                     $custom_fields = CustomFieldMeta::where('parent_id', $expense->id)->where('category',
    //                         'expenses')->get();
    //                     foreach ($custom_fields as $key) {
    //                         $custom_field = new CustomFieldMeta();
    //                         $custom_field->name = $key->name;
    //                         $custom_field->parent_id = $exp1->id;
    //                         $custom_field->custom_field_id = $key->custom_field_id;
    //                         $custom_field->category = "expenses";
    //                         $custom_field->save();
    //                     }
    //                     $exp2 = Expense::find($expense->id);
    //                     $exp2->recur_next_date = date_format(date_add(date_create(date("Y-m-d")),
    //                         date_interval_create_from_date_string($expense->recur_frequency . ' ' . $expense->recur_type . 's')),
    //                         'Y-m-d');
    //                     $exp2->save();
    //                 }
    //             }
    //         }
    //     }
    //     //check for recurring payroll
    //     $payrolls = Payroll::where('recurring', 1)->get();
    //     foreach ($payrolls as $payroll) {
    //         if (empty($payroll->recur_end_date)) {
    //             if ($payroll->recur_next_date == date("Y-m-d")) {
    //                 $pay1 = new Payroll();
    //                 $pay1->payroll_template_id = $payroll->payroll_template_id;
    //                 $pay1->user_id = $payroll->user_id;
    //                 $pay1->employee_name = $payroll->employee_name;
    //                 $pay1->business_name = $payroll->business_name;
    //                 $pay1->payment_method = $payroll->payment_method;
    //                 $pay1->bank_name = $payroll->bank_name;
    //                 $pay1->account_number = $payroll->account_number;
    //                 $pay1->description = $payroll->description;
    //                 $pay1->comments = $payroll->comments;
    //                 $pay1->paid_amount = $payroll->paid_amount;
    //                 $date = explode('-', date("Y-m-d"));
    //                 $pay1->date = date("Y-m-d");
    //                 $pay1->year = $date[0];
    //                 $pay1->month = $date[1];
    //                 $pay1->save();
    //                 //save payroll meta
    //                 $metas = PayrollMeta::where('payroll_id',
    //                     $payroll->id)->get();;
    //                 foreach ($metas as $key) {
    //                     $meta = new PayrollMeta();
    //                     $meta->value = $key->value;
    //                     $meta->payroll_id = $pay1->id;
    //                     $meta->payroll_template_meta_id = $key->payroll_template_meta_id;
    //                     $meta->position = $key->position;
    //                     $meta->save();
    //                 }
    //                 $pay2 = Payroll::find($payroll->id);
    //                 $pay2->recur_next_date = date_format(date_add(date_create(date("Y-m-d")),
    //                     date_interval_create_from_date_string($payroll->recur_frequency . ' ' . $payroll->recur_type . 's')),
    //                     'Y-m-d');
    //                 $pay2->save();
    //             } else {
    //                 if (date("Y-m-d") <= $payroll->recur_end_date) {
    //                     if ($payroll->recur_next_date == date("Y-m-d")) {
    //                         $pay1 = new Payroll();
    //                         $pay1->payroll_template_id = $payroll->payroll_template_id;
    //                         $pay1->user_id = $payroll->user_id;
    //                         $pay1->employee_name = $payroll->employee_name;
    //                         $pay1->business_name = $payroll->business_name;
    //                         $pay1->payment_method = $payroll->payment_method;
    //                         $pay1->bank_name = $payroll->bank_name;
    //                         $pay1->account_number = $payroll->account_number;
    //                         $pay1->description = $payroll->description;
    //                         $pay1->comments = $payroll->comments;
    //                         $pay1->paid_amount = $payroll->paid_amount;
    //                         $date = explode('-', date("Y-m-d"));
    //                         $pay1->date = date("Y-m-d");
    //                         $pay1->year = $date[0];
    //                         $pay1->month = $date[1];
    //                         $pay1->save();
    //                         //save payroll meta
    //                         $metas = PayrollMeta::where('payroll_id',
    //                             $payroll->id)->get();;
    //                         foreach ($metas as $key) {
    //                             $meta = new PayrollMeta();
    //                             $meta->value = $key->value;
    //                             $meta->payroll_id = $pay1->id;
    //                             $meta->payroll_template_meta_id = $key->payroll_template_meta_id;
    //                             $meta->position = $key->position;
    //                             $meta->save();
    //                         }
    //                         $pay2 = Payroll::find($payroll->id);
    //                         $pay2->recur_next_date = date_format(date_add(date_create(date("Y-m-d")),
    //                             date_interval_create_from_date_string($payroll->recur_frequency . ' ' . $payroll->recur_type . 's')),
    //                             'Y-m-d');
    //                         $pay2->save();
    //                     }
    //                 }
    //             }
    //         }
    //     }
    // }
}
