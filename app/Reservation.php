<?php

namespace App;

use App\Billing\PaymentGateway;
use Illuminate\Support\Collection;

class Reservation
{
    private $tickets;
    public $email;

    public function __construct(Collection $tickets, string $email)
    {
        $this->tickets = $tickets;
        $this->email = $email;
    }

    public function totalCost()
    {
        return $this->tickets->sum('price');
    }

    public function tickets()
    {
        return $this->tickets;
    }

    public function email()
    {
        return $this->email;
    }

    public function complete(PaymentGateway $paymentGateway, $paymentToken)
    {
        $paymentGateway->charge($this->totalCost(), $paymentToken);
        return Order::forTickets($this->tickets(), $this->email(), $this->totalCost());
    }

    public function cancel()
    {
        foreach($this->tickets as $ticket)
        {
            $ticket->release();
        }
    }
}