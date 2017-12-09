<?php

namespace App;

use App\Exceptions\NotEnoughTicketsException;
use Illuminate\Database\Eloquent\Model;

class Concert extends Model
{
    protected $guarded = [];
    protected $dates = ['date'];

    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at');
    }

    public function getFormattedDateAttribute()
    {
        return $this->date->format('F j, Y');
    }

    public function getFormattedStartTimeAttribute()
    {
        return $this->date->format('g:ia');
    }

    public function getTicketPriceInDollarsAttribute()
    {
        return number_format($this->ticket_price /100, 2);
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'tickets');
    }

    public function hasOrderFor($email)
    {
        return $this->orders()->where('email', $email)->count() > 0;
    }

    public function ordersFor($email)
    {
        return $this->orders()->where('email', $email)->get();
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function orderTickets($email, $ticketQuantity)
    {
        $tickets = $this->findtickets($ticketQuantity);
        return $this->createOrder($email, $tickets);
    }

    public function addTickets($quantity)
    {
        foreach(range(1, $quantity) as $i)
        {
            $this->tickets()->create([]);
        }

        return $this;
    }

    public function ticketsRemaining()
    {
        return $this->tickets()->available()->count();
    }

    public function reserveTickets($quantity)
    {
        return $this->findTickets($quantity)->each(function ($ticket) {
            $ticket->reserve();
        });
    }
    /**
     * @param $quantity
     * @return mixed
     */
    public function findtickets($quantity)
    {
        $tickets = $this->tickets()->available()->take($quantity)->get();

        if ($tickets->count() < $quantity) {
            throw new NotEnoughTicketsException;
        }
        return $tickets;
    }

    /**
     * @param $email
     * @param $tickets
     * @return Model
     */
    public function createOrder($email, $tickets)
    {
        return Order::forTickets($tickets, $email, $tickets->sum('price'));
    }
}
