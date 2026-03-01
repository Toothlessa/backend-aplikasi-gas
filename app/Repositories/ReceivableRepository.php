<?php

namespace App\Repositories;

use App\Models\Receivable;

class ReceivableRepository
{
    #create function
    public function createReceivableTransaction($data){
        return Receivable::create($data);
    }

    public function createForSource($source, array $data){
        return $source->receivables()->create($data);
    }

    public function updateReceivableTransaction(Receivable $receivable, $data){
        $receivable->fill($data);
        $receivable->save();

        return $receivable;
    }

    public function getReceivableById(int $receivableId){
        return Receivable::find($receivableId);
    }

    public function getReceivable(){
        return Receivable::all();
    }

    public function getLastInvoiceNumber(){
        return Receivable::orderBy('invoice_number', 'desc')
                ->lockForUpdate()
                ->first();
    }
}