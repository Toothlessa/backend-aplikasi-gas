<?php

namespace App\Services;

use App\Repositories\ReceivablePaymentRepository;
use Carbon\Carbon;
use Illuminate\Http\Exceptions\HttpResponseException;

class ReceivablePaymentService
{
    protected $receivablePaymentRepository;

    public function __construct(ReceivablePaymentRepository $receivablePaymentRepository)
    {
        $this->receivablePaymentRepository = $receivablePaymentRepository;
    }

    public function autoCreateReceivablePaymentFromTransaction($data){
        
        #transform data receivable payment
        $paymentDate = Carbon::now();

        #prepare data receivable payment
        $receivablePaymentData = [
            "payment_date"  => $paymentDate,
            "receivable_id" => $data["receivable_id"],
            "amount"        => $data["amount"],
            "payment_method"=> $data["payment_method"],
            "description"   => $data["description"],
        ];

        return $this->receivablePaymentRepository->createReceivablePayment($receivablePaymentData);
    }

    public function autoUpdateReceivablePaymentFromTransaction($receivableId, $data)
    {
        # validate existence data
        $receivablePayment = $this->getReceivablePaymentByReceivableId($receivableId);
        
        # prepare receivable payment data
        $receivablePaymentData = [
            "amount"        => $data["amount"],
            "payment_method"=> $data["payment_method"],
            "description"   => $data["description"],
        ];

        return $this->receivablePaymentRepository->updateReceivablePayment($receivablePayment, $receivablePaymentData);
    }

    public function getReceivablePaymentByReceivableId($receivableId)
    {
        $receivablePayment = $this->receivablePaymentRepository->getReceivablePaymentByReceivableId($receivableId);

        if(! $receivablePayment) {
            throw new HttpResponseException(response()->json([
                'error' => 'RECEIVABLE_PAYMENT_NOT_FOUND',
            ], 404));
        }

        return $receivablePayment;
    }
}