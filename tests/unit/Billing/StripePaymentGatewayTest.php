<?php

use App\Billing\StripePaymentGateway;

/**
 * @group integration
 */
class StripePaymentGatewayTest extends TestCase
{
    use PaymentGatewayContractTests;
    /**
     * @return StripePaymentGateway
     */
    public function getPaymentGateway(): StripePaymentGateway
    {
        $paymentGateway = new StripePaymentGateway(config('services.stripe.secret'));
        return $paymentGateway;
    }
}
