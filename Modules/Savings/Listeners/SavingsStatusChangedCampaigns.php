<?php

namespace Modules\Savings\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Modules\Communication\Entities\CommunicationCampaign;
use Modules\Setting\Entities\Setting;
use PDF;

/**
 * Listener: SavingsStatusChangedCampaigns
 *
 * This listener handles event-based ("triggered") campaigns
 * for Savings account status changes or transactions.
 *
 * It runs when a savings status changes OR when a transaction is performed.
 * Based on the status change, it determines which business rule applies,
 * finds all active campaigns for that rule, and sends the message via
 * SMS (Arkesel) or email (with optional PDF attachment).
 */
class SavingsStatusChangedCampaigns implements ShouldQueue
{
    public function __construct()
    {
        // No special construction logic needed
    }

    /**
     * Handle the triggered campaign.
     *
     * @param object $event  The event contains:
     *                       - $event->savings (Savings model instance)
     *                       - $event->previous_status (previous status string)
     *                       - $event->savings_transaction_id (int, 0 if none)
     */
    public function handle($event)
    {
        $savings = $event->savings;
        $previous_status = $event->previous_status;
        $savings_transaction_id = $event->savings_transaction_id;
        $communication_campaign_business_rule_id = '';

        /**
         * Map status changes (and transaction conditions) to business rule IDs.
         * These IDs match "communication_campaign_business_rule_id" in the campaigns table.
         */
        if ($savings->status == 'submitted' && empty($previous_status)) {
            $communication_campaign_business_rule_id = 22;
        }
        if ($savings->status == 'pending' && empty($previous_status)) {
            $communication_campaign_business_rule_id = 22;
        }
        if ($savings->status == 'rejected' && $previous_status == 'submitted') {
            $communication_campaign_business_rule_id = 23;
        }
        if ($savings->status == 'approved' && $previous_status == 'submitted') {
            $communication_campaign_business_rule_id = 24;
        }
        if ($savings->status == 'active' && $previous_status == 'approved') {
            $communication_campaign_business_rule_id = 25;
        }
        if ($savings->status == 'dormant') {
            $communication_campaign_business_rule_id = 26;
        }
        if ($savings->status == 'inactive') {
            $communication_campaign_business_rule_id = 27;
        }
        if ($savings->status == 'closed' && $previous_status == 'active') {
            $communication_campaign_business_rule_id = 28;
        }
        if ($savings_transaction_id != 0 && $previous_status == "") {
            $communication_campaign_business_rule_id = 29; // Possibly deposit
        }
        if ($savings_transaction_id != 0 && $previous_status == "withdrawal") {
            $communication_campaign_business_rule_id = 30; // Withdrawal
        }

        /**
         * Retrieve all active "triggered" campaigns that match this business rule.
         */
        $campaigns = CommunicationCampaign::where('trigger_type', 'triggered')
            ->where('status', 'active')
            ->where('communication_campaign_business_rule_id', $communication_campaign_business_rule_id)
            ->get();

        foreach ($campaigns as $key) {
            /**
             * If campaign type is SMS, send via Arkesel.
             */
            if ($key->campaign_type == 'sms') {
                if (!empty($savings->client->mobile)) {
                    // Replace template tags in message body
                    $description = template_replace_tags([
                        "body" => $key->description,
                        "client_id" => $savings->client_id,
                        "savings_id" => $savings->id,
                        "savings_transaction_id" => $savings_transaction_id
                    ]);

                    try {
                        // Load gateway credentials if provided
                        $smsGateway = $key->sms_gateway_id
                            ? \Modules\Communication\Entities\SmsGateway::find($key->sms_gateway_id)
                            : null;

                        $arkesel = $smsGateway
                            ? new \Modules\Client\Drivers\Arkesel($smsGateway->key, $smsGateway->sender)
                            : new \Modules\Client\Drivers\Arkesel();

                        // Format Ghana number: 233XXXXXXXXX
                        $formattedMobile = '233' . ltrim($savings->client->mobile, '0');

                        // Send SMS
                        $response = $arkesel->send($description, [$formattedMobile]);

                    } catch (\Exception $e) {
                        $response = $e->getMessage();
                        \Log::error('Arkesel SMS error: ' . $response);
                    }

                    // Log the campaign send result
                    log_campaign([
                        'created_by_id' => Auth::id(),
                        'client_id' => $savings->client_id,
                        'communication_campaign_id' => $key->id,
                        'campaign_type' => $key->campaign_type,
                        'description' => $description,
                        'send_to' => $savings->client->mobile,
                        'status' => 'sent',
                        'campaign_name' => $key->name,
                        'response' => $response
                    ]);
                }
            }

            /**
             * If campaign type is Email, send via Laravel's Mail.
             */
            if ($key->campaign_type == 'email') {
                if (!empty($savings->client->email)) {
                    // Replace template tags in message body
                    $description = template_replace_tags([
                        "body" => $key->description,
                        "client_id" => $savings->client_id,
                        "savings_id" => $savings->id
                    ]);

                    $email = $savings->client->email;
                    $subject = $key->subject;
                    $attachment_type = $key->communication_campaign_attachment_type_id;

                    // Send email
                    Mail::send([], [], function ($message) use ($email, $description, $subject, $attachment_type, $savings) {
                        $message->subject($subject);
                        $message->from(
                            Setting::where('setting_key', 'core.company_email')->first()->setting_value,
                            Setting::where('setting_key', 'core.company_name')->first()->setting_value
                        );
                        $message->to($email);

                        // Attach PDF statement if needed
                        if ($attachment_type == '3') {
                            $pdf = PDF::loadView('savings::savings_statement.pdf', compact('savings'))
                                ->setPaper('a4', 'landscape');

                            $message->attachData(
                                $pdf->output(),
                                trans_choice('savings::general.savings', 1) . ' ' . trans_choice('savings::general.statement', 1) . ".pdf",
                                ['mime' => 'application/pdf']
                            );
                        }
                    });

                    // Log email campaign send
                    log_campaign([
                        'created_by_id' => Auth::id(),
                        'client_id' => $savings->client_id,
                        'communication_campaign_id' => $key->id,
                        'campaign_type' => $key->campaign_type,
                        'description' => $description,
                        'send_to' => $savings->client->email,
                        'status' => 'sent',
                        'campaign_name' => $key->name
                    ]);
                }
            }
        }
    }
}
