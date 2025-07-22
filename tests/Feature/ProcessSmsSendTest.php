<?php
namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Modules\Communication\Entities\SmsGateway;
use Modules\Client\Entities\Client;
use Modules\Communication\Entities\CommunicationLog;

class ProcessSmsSendTest extends TestCase
{
    // use RefreshDatabase;

    /** @test */
    public function it_sends_hello_sms_to_specific_number_with_arkesel()
    {
        // Create a dummy gateway
        // $gateway = SmsGateway::create([
        //     'key' => 'test-key',
        //     'sender' => 'TestSender',
        //     'active' => true,
        // ]);

        // Use Arkesel driver (should pick credentials from DB)
        $arkesel = new \Modules\Client\Drivers\Arkesel();
        $message = 'hello';
        $recipient = '254713723353';

        // Actually call the send function (mocking recommended in real tests)
        $response = $arkesel->send($message, [$recipient]);

        // Assert the response is not empty (or mock and assert expected value)
        $this->assertNotEmpty($response);
    }
}
