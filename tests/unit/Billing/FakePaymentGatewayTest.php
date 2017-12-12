<?php

use App\Billing\FakePaymentGateway;

class FakePaymentGatewayTest extends TestCase
{
    use PaymentGatewayContractTests;

    /**
     * @return FakePaymentGateway
     */
    public function getPaymentGateway(): FakePaymentGateway
    {
        $paymentGateway = new FakePaymentGateway;
        return $paymentGateway;
    }

    /** @test */
    function running_a_hook_before_the_first_charge()
    {
        $paymentGateway = $this->getPaymentGateway();
        $timesCallbackRan = 0;

        $paymentGateway->beforeFirstCharge(function ($paymentGateway) use (&$timesCallbackRan) {
            $timesCallbackRan++;
            $paymentGateway->charge(2500, $paymentGateway->getValidTestToken());
            $this->assertEquals(2500, $paymentGateway->totalCharges());
        });

        $paymentGateway->charge(2500, $paymentGateway->getValidTestToken());
        $this->assertEquals(1, $timesCallbackRan);
        $this->assertEquals(5000, $paymentGateway->totalCharges());
    }
}
