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
    //     return [
    //         # customer response
    //         'nik'                   => $this['customer']->nik,
    //         'customer_name'         => $this['customer']->customer_name,
    //         # item response
    //         'item_name'             => $this['master_item']->item_name,
    //         # transaction response
    //         'trx_number'            => $this['transaction']->trx_number,
    //         'description'           => $this['transaction']->description,
    //         'stock_id'              => $this['transaction']->stock_id,
    //         'quantity'              => $this['transaction']->quantity,
    //         'amount'                => $this['transaction']->amount,
    //         'total'                 => $this['transaction']->total,
    //         'created_by'            => $this['transaction']->created_by,
    //         'created_at'            => date("h:i:s", strtotime($this['transaction']->created_at)),
    //         # payment response
    //         'payment_method'        => $this['receivable_payment']->payment_method,
    //         'paid_amount'           => $this['receivable_payment']->amount,
    //     ];
    return [
            'trx_number' => $this->trx_number,
            'description'=>$this->description,
            'quantity'   => $this->quantity,
            'amount'     => $this->amount,
            'total'      => $this->total,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at,

            'customer' => [
                'nik'   => $this->customer->nik,
                'name' => $this->customer->customer_name,
            ],

            'item' => [
                'item_name'   => $this->masterItem->item_name,
            ]
        ];
    }
}
