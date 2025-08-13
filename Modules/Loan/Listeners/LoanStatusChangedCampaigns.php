<?php

namespace Modules\Loan\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Modules\Communication\Entities\CommunicationCampaign;
use Illuminate\Support\Facades\Mail;
use Modules\Setting\Entities\Setting;
use PDF;

/**
 * Listener: LoanStatusChangedCampaigns
 *
 * This listener reacts to loan status changes and automatically runs
 * any "triggered" communication campaigns that match the status transition.
 *
 * It is part of the event-driven ("triggered") campaign system.
 * The trigger_type = 'triggered' ensures that campaigns are not
 * manually run, but are instead executed when specific loan events occur.
 */
class LoanStatusChangedCampaigns implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * This is an event subscriber class that will be queued when triggered.
     */
    public function __construct()
    {
        // No dependencies injected here
    }

    /**
     * Handle the event when a loan's status changes.
     *
     * @param object $event  The event carrying loan, previous_status, and loan_transaction_id
     */
    public function handle($event)
    {
        $loan = $event->loan;                     // Loan model instance
        $previous_status = $event->previous_status; // The status before this change
        $loan_transaction_id = $event->loan_transaction_id; // Optional related transaction
        $communication_campaign_business_rule_id = ''; // Will store the matched rule

        /**
         * Map specific status transitions to predefined Business Rule IDs.
         * These IDs are stored in CommunicationCampaign records and determine
         * which campaigns should run.
         *
         * Business Rule ID examples:
         * 15 = Loan submitted/pending
         * 16 = Loan rejected
         * 17 = Loan approved
         * 18 = Loan activated
         * 19 = Loan rescheduled
         * 20 = Loan closed
         * 31 = Loan transaction made
         */
        if ($loan->status == 'submitted' && empty($previous_status)) {
            $communication_campaign_business_rule_id = 15;
        }
        if ($loan->status == 'pending' && empty($previous_status)) {
            $communication_campaign_business_rule_id = 15;
        }
        if ($loan->status == 'rejected' && $previous_status == 'submitted') {
            $communication_campaign_business_rule_id = 16;
        }
        if ($loan->status == 'approved' && $previous_status == 'submitted') {
            $communication_campaign_business_rule_id = 17;
        }
        if ($loan->status == 'active' && $previous_status == 'approved') {
            $communication_campaign_business_rule_id = 18;
        }
        if ($loan->status == 'rescheduled') {
            $communication_campaign_business_rule_id = 19;
        }
        if ($loan->status == 'closed' && $previous_status == 'active') {
            $communication_campaign_business_rule_id = 20;
        }
        if ($loan_transaction_id != 0) {
            $communication_campaign_business_rule_id = 31;
        }

        /**
         * Retrieve campaigns that:
         * - Are "triggered" type (event-driven)
         * - Are active
         * - Match the calculated business rule ID
         */
        $campaigns = CommunicationCampaign::where('trigger_type', 'triggered')
            ->where('status', 'active')
            ->where('communication_campaign_business_rule_id', $communication_campaign_business_rule_id)
            ->get();

        foreach ($campaigns as $key) {
            /**
             * ===== SMS CAMPAIGNS =====
             * Replace placeholders in the SMS body with real data, send via Arkesel,
             * and log the message.
             */
            if ($key->campaign_type == 'sms') {
                if (!empty($loan->client->mobile)) {
                    // Replace template tags in SMS body
                    $description = template_replace_tags([
                        "body" => $key->description,
                        "client_id" => $loan->client_id,
                        "loan_id" => $loan->id,
                        "loan_transaction_id" => $loan_transaction_id
                    ]);

                    try {
                        // Determine SMS gateway (default or specific)
                        $smsGateway = $key->sms_gateway_id
                            ? \Modules\Communication\Entities\SmsGateway::find($key->sms_gateway_id)
                            : null;

                        // Create Arkesel SMS client
                        $arkesel = $smsGateway
                            ? new \Modules\Client\Drivers\Arkesel($smsGateway->key, $smsGateway->sender)
                            : new \Modules\Client\Drivers\Arkesel();

                        // Format phone number to Ghana's +233 format
                        $formattedMobile = '233' . ltrim($loan->client->mobile, '0');

                        // Send SMS
                        $response = $arkesel->send($description, [$formattedMobile]);
                    } catch (\Exception $e) {
                        $response = $e->getMessage();
                        \Log::error('Arkesel SMS error: ' . $response);
                    }

                    // Log campaign activity
                    log_campaign([
                        'created_by_id' => Auth::id(),
                        'client_id' => $loan->client_id,
                        'communication_campaign_id' => $key->id,
                        'campaign_type' => $key->campaign_type,
                        'description' => $description,
                        'send_to' => $loan->client->mobile,
                        'status' => 'sent',
                        'campaign_name' => $key->name,
                        'response' => $response
                    ]);
                }
            }

            /**
             * ===== EMAIL CAMPAIGNS =====
             * Replace placeholders in email body, send email with optional loan schedule attachment,
             * and log the message.
             */
            if ($key->campaign_type == 'email') {
                if (!empty($loan->client->email)) {
                    // Replace template tags in email body
                    $description = template_replace_tags([
                        "body" => $key->description,
                        "client_id" => $loan->client_id,
                        "loan_id" => $loan->id
                    ]);

                    $email = $loan->client->email;
                    $subject = $key->subject;
                    $attachment_type = $key->communication_campaign_attachment_type_id;

                    // Send email
                    Mail::send([], [], function ($message) use ($email, $description, $subject, $attachment_type, $loan) {
                        $message->subject($subject);
                        $message->from(
                            Setting::where('setting_key', 'core.company_email')->first()->setting_value,
                            Setting::where('setting_key', 'core.company_name')->first()->setting_value
                        );
                        $message->to($email);

                        // Attach loan schedule PDF if specified
                        if ($attachment_type == '1') {
                            $pdf = PDF::loadView('loan::loan_schedule.pdf', compact('loan'))
                                ->setPaper('a4', 'landscape');
                            $message->attachData(
                                $pdf->output(),
                                trans_choice('loan::general.loan', 1) . ' ' . trans_choice('loan::general.schedule', 1) . ".pdf",
                                ['mime' => 'application/pdf']
                            );
                        }
                    });

                    // Log email campaign
                    log_campaign([
                        'created_by_id' => Auth::id(),
                        'client_id' => $loan->client_id,
                        'communication_campaign_id' => $key->id,
                        'campaign_type' => $key->campaign_type,
                        'description' => $description,
                        'send_to' => $loan->client->email,
                        'status' => 'sent',
                        'campaign_name' => $key->name
                    ]);
                }
            }
        }
    }
}
