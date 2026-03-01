<?php

namespace App\Services;

use App\Repositories\ReceivableRepository;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Exceptions\HttpResponseException;

class ReceivableService
{
    protected $receivableRepository;
    protected $receivableItemService;
    protected $receivablePaymentService;

    public function __construct(ReceivableRepository $receivableRepository, 
    ReceivableItemService $receivableItemService,
    ReceivablePaymentService $receivablePaymentService)
    {
        $this->receivableRepository = $receivableRepository;
        $this->receivableItemService = $receivableItemService;
        $this->receivablePaymentService = $receivablePaymentService;
    }

    public function createReceivableTransaction($data)
    {
        return DB::transaction(function () use ($data){

            
            return $this->receivableRepository->createReceivableTransaction($data);
        });
        
    }

    public function autoCreateReceivableFromTransaction($transaction, $data) {
        return DB::transaction(function () use ($transaction, $data) {            
            # Generate Invoice Number
            $invoice_number = $this->generateInvoiceNumber();
            
            # Set Invoice Date
            $invoice_date   = Carbon::now();
            $due_date       = Carbon::now()->addDays(30);

            # Calculate total amount receivable
            $total_amount = $data['price'] * $data['quantity'];

            # Set Paid Amount
            $paymentResult = $this->resolvePaymentStatus(
                $data['payment_method'],
                $total_amount,
                $data['paid_amount'] ?? null
            );
            
            # Prepare Receivable Data
            $receivableData = [
                # Validate in this service
                "invoice_number"    => $invoice_number,
                "invoice_date"      => $invoice_date,
                "due_date"          => $due_date,
                "total_amount"      => $total_amount,
                # Validate in Transaction Service
                "customer_id"       => $data["customer_id"],
                "status"            => $paymentResult['status'],
                "description"       => $data["description"],
                "paid_amount"       => $paymentResult['paid_amount'],
                
                # remaining amount handled in models
                # source and source_id handled in morph models
            ];
            
            # automaticaly create source for receivable
            $receivable = $this->receivableRepository->createForSource($transaction, $receivableData);

            # prepare receivable item data
            $receivableItemData = [
                "receivable_id" => $receivable->id,
                "item_id"       => $data["item_id"],
                "quantity"      => $data["quantity"],
                "price"         => $data["price"],
            ];
            
            # create receivable item
            $this->receivableItemService->autoCreateReceivableItemFromTransaction($receivableItemData);

            # prepare receivable payment data
            $receivablePaymentData = [
                "receivable_id" => $receivable->id,
                "amount"        => $paymentResult['paid_amount'],
                "payment_method"=> $data["payment_method"],
                "description"   => "Auto Create Payment " . $invoice_number . " payment method " . $data["payment_method"],
            ];
            
            # create receivable payment
            $this->receivablePaymentService->autoCreateReceivablePaymentFromTransaction($receivablePaymentData);
            
            # return receivable with receivable payment
            // return $receivable->load('receivablePayment');
            return $receivable;
        });
    }

    public function autoUpdateReceivableFromTransaction($id, $data)
    {
        return DB::transaction(function () use ($id, $data) {
            # validate data existence
            $receivable = $this->getReceivableById($id);

            /**
             * Transform data receivable
             * - Auto update data receivable from transaction
             */

            # Calculate total amount receivable
            $total_amount = $data['amount'] * $data['quantity'];

            # Set Paid Amount
            $paymentResult = $this->resolvePaymentStatus(
                $data['payment_method'],
                $total_amount,
                $data['paid_amount'] ?? null
            );

            # Prepare Receivable Data
            $receivableData = [
                "receivable_id"     => $receivable->id,
                # Validate in this service
                "total_amount"      => $total_amount,
                # Validate in Transaction Service
                "customer_id"       => $data["customer_id"],
                "status"            => $paymentResult['status'],
                "description"       => $data["description"],
                "paid_amount"       => $paymentResult['paid_amount'],
                
                # remaining amount handled in models
            ];

            $receivable = $this->receivableRepository->updateReceivableTransaction($receivable, $receivableData);

            $receivableItemData = [
                "receivable_id" => $receivable->id,
                "item_id"       => $data["item_id"],
                "quantity"      => $data["quantity"],
                "price"         => $data["price"],
            ];

            $this->receivableItemService->autoUpdateReceivableItemFromTransaction($receivable->id, $receivableItemData);

            $receivablePaymentData = [
                "receivable_id" => $receivable->id,
                "amount"        => $paymentResult['paid_amount'],
                "payment_method"=> $data["payment_method"],
                "description"   => "Payment " . $receivable->invoice_number . " payment method " . $data["payment_method"],
            ];  

            $this->receivablePaymentService->autoUpdateReceivablePaymentFromTransaction($receivable->id, $receivablePaymentData);
            
            # return receivable with receivable payment
            return $receivable->load('receivablePayment');
        });
    }

    public function getReceivableById($id)
    {
        $receivable = $this->receivableRepository->getReceivableById($id);

        if(! $receivable) {
            throw new HttpResponseException(response()->json([
                'error' => 'RECEIVABLE_NOT_FOUND',
            ], 404));
        }

        return $receivable;
    }


    public function getLastReceivableTransactions(){
        return $this->receivableRepository->getLastInvoiceNumber();
    }

     public function generateInvoiceNumber(): string{
        $now = Carbon::now();
        $prefix = 'INV';
        $period = $now->format('Ymd'); // 20260231

        return DB::transaction(function () use ($prefix, $period) {

            // Ambil invoice terakhir di bulan ini
            $lastInvoiceNumber = $this->getLastReceivableTransactions();

            $nextSequence = 1;

            if ($lastInvoiceNumber) {
                $lastSequence = (int) substr($lastInvoiceNumber, -4);
                $nextSequence = $lastSequence + 1;
            }

            return sprintf(
                '%s-%s-%04d',
                $prefix,
                $period,
                $nextSequence
            );
        });
    }

    private function resolvePaymentStatus(string $paymentMethod, 
                                          float $totalAmount, 
                                          ?float $paidAmount = null): array
    {
        switch ($paymentMethod) {
            case 'CASH':
                return [
                    'status'      => 'PAID',
                    'paid_amount' => $totalAmount,
                ];

            case 'PARTIAL':
                return [
                    'status'      => 'PARTIAL',
                    'paid_amount' => $paidAmount ?? 0,
                ];

            default:
                return [
                    'status'      => 'UNPAID',
                    'paid_amount' => 0,
                ];
        }
    }

}