<?php

namespace App\Http\Resources\Transaction;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        \Log::info("TransactionResource receivables: ", ['data' => $this->receivables]);
    return [
            'id' => $this->id,
            'trx_number' => $this->trx_number,
            'description'=>$this->description,
            'quantity'   => $this->quantity,
            'amount'     => $this->amount,
            'total'      => $this->total,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at->format('d-M-Y H:i:s'),

            'customer_id' => $this->customer_id,
            'customer' => [
                'nik'   => $this->customer->nik,
                'name' => $this->customer->customer_name,
            ],

            'item_id' => $this->item_id,
            'item' => [
                'item_name'   => $this->masterItem->item_name,
            ],

            'receivable' => [
                'id'               => $this->receivables?->id,
                'invoice_number'   => $this->receivables?->invoice_number,
                'total_amount'     => $this->receivables?->total_amount,
                'paid_amount'      => $this->receivables?->paid_amount,
                'remaining_amount' => $this->receivables?->remaining_amount,
                'status'           => $this->receivables?->status,
                'receivable_items' => $this->receivables?->receivableItems?->map(function ($item) {
                    return [
                        'id'       => $item->id,
                        'item_id'  => $item->item_id,
                        'qty'      => $item->qty,
                        'price'    => $item->price,
                        'subtotal' => $item->subtotal,
                    ];
                }),
                'receivable_payments' => $this->receivables?->receivablePayment?->map(function ($payment) {
                    return [
                        'id'             => $payment->id,
                        'amount'         => $payment->amount,
                        'payment_method' => $payment->payment_method,
                        'payment_date'   => $payment->payment_date,
                    ];
                }),
            ]
        ];
    }
}


