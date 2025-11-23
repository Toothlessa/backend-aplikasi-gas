<?php

namespace App\Services;

use App\Repositories\AssetOwnerRepository;
use Carbon\Carbon;
use Illuminate\Http\Exceptions\HttpResponseException;

class AssetOwnerService
{
    protected $repository;

    public function __construct( AssetOwnerRepository $repository) 
    {
        $this->repository = $repository;
    }

    public function create($data, $user)
    {
        $this->validateNameExists($data['name']);

        $assetOwner = array_merge($data, [
            'created_by' => $user->id,
        ]);

        return $this->repository->create($assetOwner)->refresh();
    }

    public function update($id, $data, $user)
    {
        $assetOwner = $this->findById($id);

        if($data['name'] != $assetOwner->name) {
            $this->validateNameExists($data['name']);
        }

        $newAssetOwner = array_merge($data, [
            'updated_by' => $user->idl
        ]);

        return $this->repository->update($assetOwner, $newAssetOwner);

    }

    public function findById($id)
    {
        $assetOwner = $this->repository->findById($id);

        if(!$assetOwner) {
            throw new HttpResponseException(response()->json([
                'errors' => 'OWNER_NOT_FOUND',
            ])->setStatusCode(404));
        }

        return $assetOwner;
    }

    public function getAll()
    {
        $assetOWner = $this->repository->getAll();

        if(!$assetOWner) {
            throw new HttpResponseException(response()->json([
                'errors' => 'NO_OWNER_FOUND',
            ])->setStatusCode(404));
        }
        return $assetOWner;
    }

    public function inactiveOwner($id, $user)
    {
        $assetOwner = $this->findById($id);

        if($assetOwner->active_flag == 'Y') {
            $assetOwner->active_flag = 'N';
            $assetOwner->inactive_date = Carbon::now();
        } else {
            $assetOwner->active_flag = 'Y';
            $assetOwner->inactive_date = NULL;
        }

        $assetOwner->updated_by = $user->id;
        $assetOwner->save();
        return $assetOwner;
    }

    public function validateNameExists($assetOwnerName)
    {
        $assetOwnerName = $this->repository->validateOwnerAssetNameExists($assetOwnerName);
        
        if($assetOwnerName) {
            throw new HttpResponseException(response()->json([
                'errors' => 'OWNER_NAME_EXISTS',
            ])->setStatusCode(400));
        }

        return $assetOwnerName;
    }
}