<?php

namespace App\Services;

use App\Repositories\CategoryItemRepository;
use App\Repositories\MasterItemRepository;
use Carbon\Carbon;
use Illuminate\Http\Exceptions\HttpResponseException;

class CategoryItemService
{
    protected $repository;
    protected $masterItemRepository;
    public function __construct(CategoryItemRepository $repository,
                                MasterItemRepository $masterItemRepository)
    {
        $this->repository = $repository;
        $this->masterItemRepository = $masterItemRepository;
    }

    public function create($data, $user)
    {
        
        $this->validateCategoryName($data["name"]);
        $this->validateCategoryItemPrefix($data['prefix']);

        $data = array_merge($data, [
            'created_by' => $user->id,
        ]);

        return $this->repository->create($data);
    }

    public function update($id, $data, $user)
    {
        if(isset($data['name'])) {
            $this->validateCategoryName($data["name"]);
        }

        $categoryItem = $this->repository->findById($id);

        $newData = array_merge($data, [
            "updated_by" => $user->id,
        ]);

        return $this->repository->update($categoryItem,$newData);
    }

    public function delete($id)
    {
        $categoryItem = $this->getCategoryItemId($id);

        $this->validateCategoryMasterItem($id);
        $this->repository->delete($categoryItem);

        return response()->json([
            'data' => true
        ])->setStatusCode(200);
    }

    public function getCategoryItemId($id)
    {
        $data = $this->repository->findById($id);

        if(!$data) {
            throw new HttpResponseException(response()->json([
                "errors" => "CATEGORY_ITEM_NOT_FOUND"
            ])->setStatusCode(404));
        }

        return $data;
    }

    public function validateCategoryName(string $name)
    {
        $data =  $this->repository->validateCategoryNameExists(($name));

        if($data) {
            throw new HttpResponseException(response()->json( [
                "errors" => "NAME_EXISTS"
            ])->setStatusCode(400));
        }

        return true;
    }

    public function validateCategoryMasterItem($id)
    {

        $data =  $this->repository->validateCategoryMasterItem($id);
        if($data) {
            throw new HttpResponseException(response()->json( [
                "errors" => "CATEGORY_EXIST_IN_ITEMS"
            ])->setStatusCode(400));
        }

        return true;
    }

    public function validateCategoryItemPrefix($prefix)
    {
        $data = $this->repository->validateCategoryItemPrefix($prefix);
        if($data) {
            throw new HttpResponseException(response()->json([
                'errors' => 'PREFIX_EXISTS',
            ])->setStatusCode(400));
        }

        return true;
    }

    public function getAllCategoryItem()
    {
        return $this->repository->getAllCategoryItem();
    }

    public function getActiveCategoryItem()
    {
        return $this->repository->getActiveCategoryItem();
    }

    public function inactiveCategoryItem($id, $user)
    {
        $categoryItem = $this->getCategoryItemId($id);

        if($categoryItem->active_flag == 'Y') {
            $categoryItem->active_flag = 'N';
            $categoryItem->inactive_date = Carbon::now();
        } else {
            $categoryItem->active_flag = 'Y';
            $categoryItem->inactive_date = NULL;
        }

        $categoryItem->updated_by = $user->id;
        $categoryItem->save();
        return $categoryItem;
    }
}