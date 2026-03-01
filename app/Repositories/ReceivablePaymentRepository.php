<?php

namespace App\Repositories;

use App\Models\ReceivablePayment;

class ReceivablePaymentRepository
{
    public function createReceivablePayment($data)
    {
        return ReceivablePayment::create($data);
    }

    public function updateReceivablePayment(ReceivablePayment $receivablePayment, $data)
    {
        $receivablePayment->fill($data);
        $receivablePayment->save();

        return $receivablePayment;
    }

    public function getReceivablePaymentByReceivableId($receivableId)
    {
        return ReceivablePayment::where('receivable_id', $receivableId)->first();
    }
}