<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Http\Requests\TransferFundsRequest;
use App\Http\Services\FundsTransferService;
use App\Http\Interfaces\CurrencyRateService;
use App\Models\Account;
use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;
use Tests\TestCase;
use Illuminate\Http\JsonResponse;

class FundsTransferServiceTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private (MockInterface&LegacyMockInterface)|CurrencyRateService $currencyRateServiceMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->currencyRateServiceMock = Mockery::mock(CurrencyRateService::class);
    }

    public function testSuccessfulTransfer()
    {
        $email = 'mintos@mintos.com';

        $client = Client::factory()->create(['email' => $email]);
        $senderAccount = Account::factory()->create(['client_id' => $client->id, 'currency' => 'EUR', 'balance' => 100]);
        $recipientAccount = Account::factory()->create(['client_id' => $client->id, 'currency' => 'SEK', 'balance' => 30]);

        $amount = '15.00';
        $currencyRates = ['EUR' => 2, 'GBP' => 0.85, 'SEK' => 1.5];

        $this->currencyRateServiceMock->shouldReceive('getCurrencyRatesForUSD')
            ->once()
            ->andReturn($currencyRates);

        $fundsTransferService = new FundsTransferService($this->currencyRateServiceMock);

        $request = TransferFundsRequest::create('/transfer', 'POST', [
            'sender_account_id' => $senderAccount->id,
            'recipient_account_id' => $recipientAccount->id,
            'amount' => $amount,
        ]);

        $request->headers->set('x-client-email', $email);
        $response = $fundsTransferService->create($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->status());
        $this->assertEquals('Transfer successful.', $response->getData()->message);
        $this->assertDatabaseHas('accounts', [
            'id' => $senderAccount->id,
            'balance' => '80.00',
        ]);
        $this->assertDatabaseHas('accounts', [
            'id' => $recipientAccount->id,
            'balance' => '45.00',
        ]);
    }

    public function testTransferFailsDueToUnavailableCurrencyRate()
    {
        $email = 'mintos@mintos.com';

        $client = Client::factory()->create(['email' => $email]);
        $senderAccount = Account::factory()->create(['client_id' => $client->id, 'currency' => 'EUR', 'balance' => 100]);
        $recipientAccount = Account::factory()->create(['client_id' => $client->id, 'currency' => 'JPY', 'balance' => 1000]);

        $currencyRates = [];

        $this->currencyRateServiceMock->shouldReceive('getCurrencyRatesForUSD')
            ->once()
            ->andReturn($currencyRates);

        $fundsTransferService = new FundsTransferService($this->currencyRateServiceMock);

        $request = TransferFundsRequest::create('/transfer', 'POST', [
            'sender_account_id' => $senderAccount->id,
            'recipient_account_id' => $recipientAccount->id,
            'amount' => '50',
        ]);

        $request->headers->set('x-client-email', $email);
        $response = $fundsTransferService->create($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(403, $response->status());
        $this->assertEquals('Currency rate not found.', $response->getData()->message);
    }

    public function testTransferFailsDueToInsufficientFunds()
    {
        $email = 'mintos@mintos.com';

        $client = Client::factory()->create(['email' => $email]);
        $senderAccount = Account::factory()->create(['client_id' => $client->id, 'currency' => 'SEK', 'balance' => 20]);
        $recipientAccount = Account::factory()->create(['client_id' => $client->id, 'currency' => 'EUR', 'balance' => 100]);

        $amount = '50.00';
        $currencyRates = ['EUR' => 2, 'GBP' => 0.85, 'SEK' => 1.5];

        $this->currencyRateServiceMock->shouldReceive('getCurrencyRatesForUSD')
            ->once()
            ->andReturn($currencyRates);

        $fundsTransferService = new FundsTransferService($this->currencyRateServiceMock);

        $request = TransferFundsRequest::create('/transfer', 'POST', [
            'sender_account_id' => $senderAccount->id,
            'recipient_account_id' => $recipientAccount->id,
            'amount' => $amount,
        ]);

        $request->headers->set('x-client-email', $email);
        $response = $fundsTransferService->create($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(403, $response->status());
        $this->assertEquals('Insufficient funds.', $response->getData()->message);
    }
}
