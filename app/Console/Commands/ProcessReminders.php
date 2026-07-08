<?php

namespace App\Console\Commands;


use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Modules\Setting\Entities\Setting;

class ProcessReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminders:process';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminder emails and sms';

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
    $settings = Setting::pluck('setting_value', 'setting_key');

    // Repayment reminders
    if (!empty($settings['auto_repayment_email_reminder']) && $settings['auto_repayment_email_reminder'] == 1) {
        $this->processReminders('email', Carbon::now()->addDays($settings['auto_repayment_days'])->format("Y-m-d"), $settings, 'loan_payment_reminder_email_template', 'loan_payment_reminder_subject');
    }

    if (!empty($settings['auto_repayment_sms_reminder']) && $settings['auto_repayment_sms_reminder'] == 1) {
        $this->processReminders('sms', Carbon::now()->addDays($settings['auto_repayment_days'])->format("Y-m-d"), $settings, 'loan_payment_reminder_email_template'); // using email template for sms (as per your code)
    }

    // Overdue reminders
    if (!empty($settings['auto_overdue_repayment_email_reminder']) && $settings['auto_overdue_repayment_email_reminder'] == 1) {
        $this->processReminders('email', Carbon::now()->subDays($settings['auto_overdue_repayment_days'])->format("Y-m-d"), $settings, 'missed_payment_email_template', 'missed_payment_email_subject');
    }

    if (!empty($settings['auto_overdue_repayment_sms_reminder']) && $settings['auto_overdue_repayment_sms_reminder'] == 1) {
        $this->processReminders('sms', Carbon::now()->subDays($settings['auto_overdue_repayment_days'])->format("Y-m-d"), $settings, 'missed_payment_sms_template');
    }
}

/**
 * Process repayment or overdue reminders
 */
private function processReminders($type, $due_date, $settings, $templateKey, $subjectKey = null)
{
    $schedules = $this->getSchedules($due_date);

    foreach ($schedules as $schedule) {
        $outstanding = ($schedule->total_principal - $schedule->total_principal_waived)
            + ($schedule->total_interest - $schedule->total_interest_waived)
            + ($schedule->total_fees - $schedule->total_fees_waived)
            + ($schedule->total_penalty - $schedule->total_penalty_waived);

        $paid = $schedule->principal_paid + $schedule->interest_paid + $schedule->penalty_paid + $schedule->fees_paid;

        if ($outstanding <= $paid) {
            continue; // fully paid
        }

        // Build message
        $template = $settings[$templateKey] ?? '';
        $body = $this->replacePlaceholders($template, $schedule);

        if ($type === 'email' && !empty($schedule->email)) {
            $subject = $settings[$subjectKey] ?? 'Loan Reminder';
            $this->sendEmail($schedule->email, $body, $subject, $settings);
        }

        if ($type === 'sms' && !empty($schedule->mobile)) {
            $this->sendSms($schedule->mobile, strip_tags($body));
        }
    }
}

/**
 * Query loan schedules with borrowers
 */
private function getSchedules($due_date)
{
    return DB::table("loan_schedules")
        ->leftJoin("borrowers", "borrowers.id", "loan_schedules.borrower_id")
        ->selectRaw("
            borrowers.*,
            loan_schedules.*,
            (SELECT SUM(principal) FROM loan_schedules) total_principal,
            (SELECT SUM(interest)  FROM loan_schedules) total_interest,
            (SELECT SUM(fees)  FROM loan_schedules) total_fees,
            (SELECT SUM(penalty)  FROM loan_schedules) total_penalty,
            (SELECT SUM(principal_waived) FROM loan_schedules) total_principal_waived,
            (SELECT SUM(interest_waived)  FROM loan_schedules) total_interest_waived,
            (SELECT SUM(fees_waived) FROM loan_schedules) total_fees_waived,
            (SELECT SUM(penalty_waived)  FROM loan_schedules) total_penalty_waived,
            (SELECT SUM(credit) FROM loan_transactions WHERE transaction_type='repayment' AND reversed=0 AND loan_transactions.loan_id=loan_schedules.loan_id) payments
        ")
        ->where('loan_schedules.due_date', $due_date)
        ->get();
}

/**
 * Replace placeholders in templates
 */
private function replacePlaceholders($template, $schedule)
{
    $replacements = [
        '{borrowerTitle}'       => $schedule->title,
        '{borrowerFirstName}'   => $schedule->first_name,
        '{borrowerLastName}'    => $schedule->last_name,
        '{borrowerAddress}'     => $schedule->address,
        '{borrowerUniqueNumber}'=> $schedule->unique_number,
        '{borrowerMobile}'      => $schedule->mobile,
        '{borrowerPhone}'       => $schedule->phone,
        '{borrowerEmail}'       => $schedule->email,
        '{loanNumber}'          => $schedule->loan_id,
        '{paymentAmount}'       => round(($schedule->principal + $schedule->interest + $schedule->fees + $schedule->penalty)
                                   - ($schedule->principal_paid + $schedule->interest_paid + $schedule->penalty_paid + $schedule->fees_paid), 2),
        '{paymentDate}'         => $schedule->due_date,
        '{loanPayments}'        => $schedule->payments,
        '{loanDue}'             => round(($schedule->total_principal - $schedule->total_principal_waived)
                                   + ($schedule->total_interest - $schedule->total_interest_waived)
                                   + ($schedule->total_fees - $schedule->total_fees_waived)
                                   + ($schedule->total_penalty - $schedule->total_penalty_waived), 2),
        '{loanBalance}'         => round(($schedule->total_principal - $schedule->total_principal_waived)
                                   + ($schedule->total_interest - $schedule->total_interest_waived)
                                   + ($schedule->total_fees - $schedule->total_fees_waived)
                                   + ($schedule->total_penalty - $schedule->total_penalty_waived)
                                   - $schedule->payments, 2),
    ];

    return str_replace(array_keys($replacements), array_values($replacements), $template);
}

/**
 * Send Email
 */
private function sendEmail($to, $body, $subject, $settings)
{
    Mail::send([], [], function ($message) use ($to, $body, $subject, $settings) {
        $message->from($settings['company_email'] ?? 'noreply@example.com', $settings['company_name'] ?? 'Company');
        $message->to($to);
        $message->setContentType('text/html');
        $message->setBody($body);
        $message->setSubject($subject);
    });
}

/**
 * Send SMS
 */
private function sendSms($mobile, $body)
{
    try {
        $smsGateway = \Modules\Communication\Entities\SmsGateway::first();
        $arkesel = $smsGateway
            ? new \Modules\Client\Drivers\Arkesel($smsGateway->key, $smsGateway->sender)
            : new \Modules\Client\Drivers\Arkesel();
        $formattedMobile = '233' . ltrim($mobile, '0');
        $arkesel->send($body, [$formattedMobile]);
    } catch (\Exception $e) {
        \Log::error('Arkesel SMS error: ' . $e->getMessage());
    }
}





}

    // public function handle()
    // {
    //     //check repayment reminders
    //     if (Setting::where('setting_key', 'auto_repayment_email_reminder')->value('setting_value') == 1) { {
    //         $days = Setting::where('setting_key', 'auto_repayment_days')->value('setting_value');
    //         $due_date = Carbon::now()->addDays($days)->format("Y-m-d");
    //         foreach (DB::table("loan_schedules")->leftJoin("borrowers", "borrowers.id", "loan_schedules.borrower_id")->selectRaw(DB::raw("borrowers.*,loan_schedules.*,(SELECT SUM(principal) FROM loan_schedules) total_principal,(SELECT SUM(interest)  FROM loan_schedules) total_interest,(SELECT SUM(fees)  FROM loan_schedules) total_fees,(SELECT SUM(penalty)  FROM loan_schedules) total_penalty,(SELECT SUM(principal_waived)  FROM loan_schedules) total_principal_waived,(SELECT SUM(interest_waived)  FROM loan_schedules) total_interest_waived,(SELECT SUM(fees_waived) total_fees_waived FROM loan_schedules) total_fees_waived,(SELECT SUM(penalty_waived)  FROM loan_schedules) total_penalty_waived,(SELECT SUM(credit) FROM loan_transactions WHERE transaction_type='repayment' AND reversed=0 AND loan_transactions.loan_id=loan_schedules.loan_id) payments"))->where('loan_schedules.due_date', $due_date)->get() as $schedule) {
    //             if (($schedule->total_principal - $schedule->total_principal_waived + $schedule->total_interest - $schedule->total_interest_waived + $schedule->total_fees - $schedule->total_fees_waived + $schedule->total_penalty - $schedule->total_penalty_waived) > ($schedule->principal_paid + $schedule->interest_paid + $schedule->penalty_paid + $schedule->fees_paid)) {

    //                 //check if borrower has email
    //                 if (!empty($schedule->email)) {
    //                     $body = Setting::where(
    //                         'setting_key',
    //                         'loan_payment_reminder_email_template'
    //                     )->value('setting_value');
    //                     $body = str_replace('{borrowerTitle}', $schedule->title, $body);
    //                     $body = str_replace('{borrowerFirstName}', $schedule->first_name, $body);
    //                     $body = str_replace('{borrowerLastName}', $schedule->last_name, $body);
    //                     $body = str_replace('{borrowerAddress}', $schedule->address, $body);
    //                     $body = str_replace('{borrowerUniqueNumber}', $schedule->unique_number, $body);
    //                     $body = str_replace('{borrowerMobile}', $schedule->mobile, $body);
    //                     $body = str_replace('{borrowerPhone}', $schedule->phone, $body);
    //                     $body = str_replace('{borrowerEmail}', $schedule->email, $body);
    //                     $body = str_replace('{loanNumber}', $schedule->loan_id, $body);
    //                     $body = str_replace(
    //                         '{paymentAmount}',
    //                         round(($schedule->principal + $schedule->interest + $schedule->fees + $schedule->penalty) - ($schedule->principal_paid + $schedule->interest_paid + $schedule->penalty_paid + $schedule->fees_paid),
    //                             2
    //                         ),
    //                         $body
    //                     );
    //                     $body = str_replace('{paymentDate}', $schedule->due_date, $body);
    //                     $body = str_replace('{loanPayments}', $schedule->payments, $body);
    //                     $body = str_replace(
    //                         '{loanDue}',
    //                         round($schedule->total_principal - $schedule->total_principal_waived + $schedule->total_interest - $schedule->total_interest_waived + $schedule->total_fees - $schedule->total_fees_waived + $schedule->total_penalty - $schedule->total_penalty_waived, 2),
    //                         $body
    //                     );
    //                     $body = str_replace(
    //                         '{loanBalance}',
    //                         round(
    //                             $schedule->total_principal - $schedule->total_principal_waived + $schedule->total_interest - $schedule->total_interest_waived + $schedule->total_fees - $schedule->total_fees_waived + $schedule->total_penalty - $schedule->total_penalty_waived - $schedule->payments,
    //                             2
    //                         ),
    //                         $body
    //                     );
    //                     Mail::send([], [], function ($message) use ($schedule, $body) {
    //                         $message->from(
    //                             Setting::where('setting_key', 'company_email')->value('setting_value'),
    //                             Setting::where('setting_key', 'company_name')->value('setting_value')
    //                         );
    //                         $message->to($schedule->email);
    //                         $headers = $message->getHeaders();
    //                         $message->setContentType('text/html');
    //                         $message->setBody($body);
    //                         $message->setSubject(Setting::where(
    //                             'setting_key',
    //                             'loan_payment_reminder_subject'
    //                         )->value('setting_value'));
    //                     });
    //                     $mail = new Email();
    //                     //$mail->user_id = Sentinel::getUser()->id;
    //                     $mail->message = $body;
    //                     $mail->subject = Setting::where(
    //                         'setting_key',
    //                         'loan_payment_reminder_subject'
    //                     )->value('setting_value');
    //                     $mail->recipients = 1;
    //                     $mail->send_to = $schedule->first_name . ' ' . $schedule->last_name . '(' . $schedule->unique_number . ')';
    //                     $mail->save();
    //                 }
    //             }
    //         }
    //     }
    //     if (Setting::where('setting_key', 'auto_repayment_sms_reminder')->value('setting_value') == 1) {
    //         $days = Setting::where('setting_key', 'auto_repayment_days')->value('setting_value');
    //         $due_date = Carbon::now()->addDays($days)->format("Y-m-d");
    //         foreach (DB::table("loan_schedules")->leftJoin("borrowers", "borrowers.id", "loan_schedules.borrower_id")->selectRaw(DB::raw("borrowers.*,loan_schedules.*,(SELECT SUM(principal) FROM loan_schedules) total_principal,(SELECT SUM(interest)  FROM loan_schedules) total_interest,(SELECT SUM(fees)  FROM loan_schedules) total_fees,(SELECT SUM(penalty)  FROM loan_schedules) total_penalty,(SELECT SUM(principal_waived)  FROM loan_schedules) total_principal_waived,(SELECT SUM(interest_waived)  FROM loan_schedules) total_interest_waived,(SELECT SUM(fees_waived) total_fees_waived FROM loan_schedules) total_fees_waived,(SELECT SUM(penalty_waived)  FROM loan_schedules) total_penalty_waived,(SELECT SUM(credit) FROM loan_transactions WHERE transaction_type='repayment' AND reversed=0 AND loan_transactions.loan_id=loan_schedules.loan_id) payments"))->where('loan_schedules.due_date', $due_date)->get() as $schedule) {
    //             if (($schedule->total_principal - $schedule->total_principal_waived + $schedule->total_interest - $schedule->total_interest_waived + $schedule->total_fees - $schedule->total_fees_waived + $schedule->total_penalty - $schedule->total_penalty_waived) > ($schedule->principal_paid + $schedule->interest_paid + $schedule->penalty_paid + $schedule->fees_paid)) {

    //                 //check if borrower has mobile
    //                 if (!empty($schedule->mobile)) {
    //                     $body = Setting::where(
    //                         'setting_key',
    //                         'loan_payment_reminder_email_template'
    //                     )->value('setting_value');
    //                     $body = str_replace('{borrowerTitle}', $schedule->title, $body);
    //                     $body = str_replace('{borrowerFirstName}', $schedule->first_name, $body);
    //                     $body = str_replace('{borrowerLastName}', $schedule->last_name, $body);
    //                     $body = str_replace('{borrowerAddress}', $schedule->address, $body);
    //                     $body = str_replace('{borrowerUniqueNumber}', $schedule->unique_number, $body);
    //                     $body = str_replace('{borrowerMobile}', $schedule->mobile, $body);
    //                     $body = str_replace('{borrowerPhone}', $schedule->phone, $body);
    //                     $body = str_replace('{borrowerEmail}', $schedule->email, $body);
    //                     $body = str_replace('{loanNumber}', $schedule->loan_id, $body);
    //                     $body = str_replace(
    //                         '{paymentAmount}',
    //                         round(($schedule->principal + $schedule->interest + $schedule->fees + $schedule->penalty) - ($schedule->principal_paid + $schedule->interest_paid + $schedule->penalty_paid + $schedule->fees_paid),
    //                             2
    //                         ),
    //                         $body
    //                     );
    //                     $body = str_replace('{paymentDate}', $schedule->due_date, $body);
    //                     $body = str_replace('{loanPayments}', $schedule->payments, $body);
    //                     $body = str_replace(
    //                         '{loanDue}',
    //                         round($schedule->total_principal - $schedule->total_principal_waived + $schedule->total_interest - $schedule->total_interest_waived + $schedule->total_fees - $schedule->total_fees_waived + $schedule->total_penalty - $schedule->total_penalty_waived, 2),
    //                         $body
    //                     );
    //                     $body = str_replace(
    //                         '{loanBalance}',
    //                         round(
    //                             $schedule->total_principal - $schedule->total_principal_waived + $schedule->total_interest - $schedule->total_interest_waived + $schedule->total_fees - $schedule->total_fees_waived + $schedule->total_penalty - $schedule->total_penalty_waived - $schedule->payments,
    //                             2
    //                         ),
    //                         $body
    //                     );
    //                     $body = strip_tags($body);
    //                     $active_sms = Setting::where('setting_key', 'active_sms')->value('setting_value');
    //                     //Handle send_sms here
    //                     //GeneralHelper::send_sms($schedule->mobile, $body);
    //                     try {
    //                         $smsGateway = \Modules\Communication\Entities\SmsGateway::first();
    //                         $arkesel = $smsGateway
    //                             ? new \Modules\Client\Drivers\Arkesel($smsGateway->key, $smsGateway->sender)
    //                             : new \Modules\Client\Drivers\Arkesel();
    //                         $formattedMobile = '233' . ltrim($schedule->mobile, '0');
    //                         $response = $arkesel->send($body, [$formattedMobile]);
    //                     } catch (\Exception $e) {
    //                         $response = $e->getMessage();
    //                         \Log::error('Arkesel SMS error: ' . $response);
    //                     }
    //                     // $sms = new Sms();
    //                     // //$sms->user_id = Sentinel::getUser()->id;
    //                     // $sms->message = $body;
    //                     // $sms->gateway = $active_sms;
    //                     // $sms->recipients = 1;
    //                     // $sms->send_to = $schedule->first_name . ' ' . $schedule->last_name . '(' . $schedule->unique_number . ')';
    //                     // $sms->save();
    //                 }
    //             }
    //         }
    //     }
    //     //check for missed repayments
    //     if (Setting::where('setting_key', 'auto_overdue_repayment_email_reminder')->value('setting_value') == 1) {
    //         $days = Setting::where('setting_key', 'auto_overdue_repayment_days')->value('setting_value');
    //         $due_date = Carbon::now()->subDays($days)->format("Y-m-d");
    //         foreach (DB::table("loan_schedules")->leftJoin("borrowers", "borrowers.id", "loan_schedules.borrower_id")->selectRaw(DB::raw("borrowers.*,loan_schedules.*,(SELECT SUM(principal) FROM loan_schedules) total_principal,(SELECT SUM(interest)  FROM loan_schedules) total_interest,(SELECT SUM(fees)  FROM loan_schedules) total_fees,(SELECT SUM(penalty)  FROM loan_schedules) total_penalty,(SELECT SUM(principal_waived)  FROM loan_schedules) total_principal_waived,(SELECT SUM(interest_waived)  FROM loan_schedules) total_interest_waived,(SELECT SUM(fees_waived) total_fees_waived FROM loan_schedules) total_fees_waived,(SELECT SUM(penalty_waived)  FROM loan_schedules) total_penalty_waived,(SELECT SUM(credit) FROM loan_transactions WHERE transaction_type='repayment' AND reversed=0 AND loan_transactions.loan_id=loan_schedules.loan_id) payments"))->where('loan_schedules.due_date', $due_date)->get() as $schedule) {
    //             if (($schedule->total_principal - $schedule->total_principal_waived + $schedule->total_interest - $schedule->total_interest_waived + $schedule->total_fees - $schedule->total_fees_waived + $schedule->total_penalty - $schedule->total_penalty_waived) > ($schedule->principal_paid + $schedule->interest_paid + $schedule->penalty_paid + $schedule->fees_paid)) {

    //                 //check if borrower has email
    //                 if (!empty($schedule->email)) {
    //                     $body = Setting::where(
    //                         'setting_key',
    //                         'missed_payment_email_template'
    //                     )->value('setting_value');
    //                     $body = str_replace('{borrowerTitle}', $schedule->title, $body);
    //                     $body = str_replace('{borrowerFirstName}', $schedule->first_name, $body);
    //                     $body = str_replace('{borrowerLastName}', $schedule->last_name, $body);
    //                     $body = str_replace('{borrowerAddress}', $schedule->address, $body);
    //                     $body = str_replace('{borrowerUniqueNumber}', $schedule->unique_number, $body);
    //                     $body = str_replace('{borrowerMobile}', $schedule->mobile, $body);
    //                     $body = str_replace('{borrowerPhone}', $schedule->phone, $body);
    //                     $body = str_replace('{borrowerEmail}', $schedule->email, $body);
    //                     $body = str_replace('{loanNumber}', $schedule->loan_id, $body);
    //                     $body = str_replace(
    //                         '{paymentAmount}',
    //                         round(($schedule->principal + $schedule->interest + $schedule->fees + $schedule->penalty) - ($schedule->principal_paid + $schedule->interest_paid + $schedule->penalty_paid + $schedule->fees_paid),
    //                             2
    //                         ),
    //                         $body
    //                     );
    //                     $body = str_replace('{paymentDate}', $schedule->due_date, $body);
    //                     $body = str_replace('{loanPayments}', $schedule->payments, $body);
    //                     $body = str_replace(
    //                         '{loanDue}',
    //                         round($schedule->total_principal - $schedule->total_principal_waived + $schedule->total_interest - $schedule->total_interest_waived + $schedule->total_fees - $schedule->total_fees_waived + $schedule->total_penalty - $schedule->total_penalty_waived, 2),
    //                         $body
    //                     );
    //                     $body = str_replace(
    //                         '{loanBalance}',
    //                         round(
    //                             $schedule->total_principal - $schedule->total_principal_waived + $schedule->total_interest - $schedule->total_interest_waived + $schedule->total_fees - $schedule->total_fees_waived + $schedule->total_penalty - $schedule->total_penalty_waived - $schedule->payments,
    //                             2
    //                         ),
    //                         $body
    //                     );
    //                     Mail::send([], [], function ($message) use ($schedule, $body) {
    //                         $message->from(
    //                             Setting::where('setting_key', 'company_email')->value('setting_value'),
    //                             Setting::where('setting_key', 'company_name')->value('setting_value')
    //                         );
    //                         $message->to($schedule->email);
    //                         $headers = $message->getHeaders();
    //                         $message->setContentType('text/html');
    //                         $message->setBody($body);
    //                         $message->setSubject(Setting::where(
    //                             'setting_key',
    //                             'missed_payment_email_subject'
    //                         )->value('setting_value'));
    //                     });
    //                     $mail = new Email();
    //                     //$mail->user_id = Sentinel::getUser()->id;
    //                     $mail->message = $body;
    //                     $mail->subject = Setting::where(
    //                         'setting_key',
    //                         'missed_payment_email_subject'
    //                     )->value('setting_value');
    //                     $mail->recipients = 1;
    //                     $mail->send_to = $schedule->first_name . ' ' . $schedule->last_name . '(' . $schedule->unique_number . ')';
    //                     $mail->save();
    //                 }
    //             }
    //         }
    //     }
    //     if (Setting::where('setting_key', 'auto_overdue_repayment_sms_reminder')->value('setting_value') == 1) {
    //         $days = Setting::where('setting_key', 'auto_overdue_repayment_days')->value('setting_value');
    //         $due_date = Carbon::now()->subDays($days)->format("Y-m-d");
    //         foreach (DB::table("loan_schedules")->leftJoin("borrowers", "borrowers.id", "loan_schedules.borrower_id")->selectRaw(DB::raw("borrowers.*,loan_schedules.*,(SELECT SUM(principal) FROM loan_schedules) total_principal,(SELECT SUM(interest)  FROM loan_schedules) total_interest,(SELECT SUM(fees)  FROM loan_schedules) total_fees,(SELECT SUM(penalty)  FROM loan_schedules) total_penalty,(SELECT SUM(principal_waived)  FROM loan_schedules) total_principal_waived,(SELECT SUM(interest_waived)  FROM loan_schedules) total_interest_waived,(SELECT SUM(fees_waived) total_fees_waived FROM loan_schedules) total_fees_waived,(SELECT SUM(penalty_waived)  FROM loan_schedules) total_penalty_waived,(SELECT SUM(credit) FROM loan_transactions WHERE transaction_type='repayment' AND reversed=0 AND loan_transactions.loan_id=loan_schedules.loan_id) payments"))->where('loan_schedules.due_date', $due_date)->get() as $schedule) {
    //             if (($schedule->total_principal - $schedule->total_principal_waived + $schedule->total_interest - $schedule->total_interest_waived + $schedule->total_fees - $schedule->total_fees_waived + $schedule->total_penalty - $schedule->total_penalty_waived) > ($schedule->principal_paid + $schedule->interest_paid + $schedule->penalty_paid + $schedule->fees_paid)) {

    //                 //check if borrower has email
    //                 if (!empty($schedule->mobile)) {
    //                     $body = Setting::where(
    //                         'setting_key',
    //                         'missed_payment_sms_template'
    //                     )->value('setting_value');
    //                     $body = str_replace('{borrowerTitle}', $schedule->title, $body);
    //                     $body = str_replace('{borrowerFirstName}', $schedule->first_name, $body);
    //                     $body = str_replace('{borrowerLastName}', $schedule->last_name, $body);
    //                     $body = str_replace('{borrowerAddress}', $schedule->address, $body);
    //                     $body = str_replace('{borrowerUniqueNumber}', $schedule->unique_number, $body);
    //                     $body = str_replace('{borrowerMobile}', $schedule->mobile, $body);
    //                     $body = str_replace('{borrowerPhone}', $schedule->phone, $body);
    //                     $body = str_replace('{borrowerEmail}', $schedule->email, $body);
    //                     $body = str_replace('{loanNumber}', $schedule->loan_id, $body);
    //                     $body = str_replace(
    //                         '{paymentAmount}',
    //                         round(($schedule->principal + $schedule->interest + $schedule->fees + $schedule->penalty) - ($schedule->principal_paid + $schedule->interest_paid + $schedule->penalty_paid + $schedule->fees_paid),
    //                             2
    //                         ),
    //                         $body
    //                     );
    //                     $body = str_replace('{paymentDate}', $schedule->due_date, $body);
    //                     $body = str_replace('{loanPayments}', $schedule->payments, $body);
    //                     $body = str_replace(
    //                         '{loanDue}',
    //                         round($schedule->total_principal - $schedule->total_principal_waived + $schedule->total_interest - $schedule->total_interest_waived + $schedule->total_fees - $schedule->total_fees_waived + $schedule->total_penalty - $schedule->total_penalty_waived, 2),
    //                         $body
    //                     );
    //                     $body = str_replace(
    //                         '{loanBalance}',
    //                         round(
    //                             $schedule->total_principal - $schedule->total_principal_waived + $schedule->total_interest - $schedule->total_interest_waived + $schedule->total_fees - $schedule->total_fees_waived + $schedule->total_penalty - $schedule->total_penalty_waived - $schedule->payments,
    //                             2
    //                         ),
    //                         $body
    //                     );
    //                     $body = strip_tags($body);
    //                     $active_sms = Setting::where('setting_key', 'active_sms')->value('setting_value');
    //                     //Handle send_sms here
    //                     //GeneralHelper::send_sms($schedule->mobile, $body);
    //                     try {
    //                         $smsGateway = \Modules\Communication\Entities\SmsGateway::first();
    //                         $arkesel = $smsGateway
    //                             ? new \Modules\Client\Drivers\Arkesel($smsGateway->key, $smsGateway->sender)
    //                             : new \Modules\Client\Drivers\Arkesel();
    //                         $formattedMobile = '233' . ltrim($schedule->mobile, '0');
    //                         $response = $arkesel->send($body, [$formattedMobile]);
    //                     } catch (\Exception $e) {
    //                         $response = $e->getMessage();
    //                         \Log::error('Arkesel SMS error: ' . $response);
    //                     }
    //                     $sms = new Sms();
    //                     //$sms->user_id = Sentinel::getUser()->id;
    //                     $sms->message = $body;
    //                     $sms->gateway = $active_sms;
    //                     $sms->recipients = 1;
    //                     $sms->send_to = $schedule->first_name . ' ' . $schedule->last_name . '(' . $schedule->unique_number . ')';
    //                     $sms->save();
    //                 }
    //             }
    //         }
    //     }
    // }
    // }