<?php

namespace App\Services;

use App\Repositories\CategoryItemRepository;
use Carbon\Carbon;
use Illuminate\Http\Exceptions\HttpResponseException;

class CategoryItemService
{
    protected $repository;
    public function __construct(CategoryItemRepository $repository)
    {
        $this->repository = $repository;
    }

    public function create($data, $user){
        
        $this->validateCategoryName($data["name"]);
        $this->validateCategoryItemPrefix($data['prefix']);

        $categoryItem = [
            'name' => $data['name'],
            'prefix' => $data['prefix'],
            'created_by' => $user->id,
        ];

        return $this->repository->create($categoryItem);
    }

    public function update($id, $data, $user)
    {
        if(isset($data['name'])) {
            $this->validateCategoryName($data["name"]);
        }

        $categoryItem = $this->repository->findById($id);

        $newCategoryItem = [
            'name' => $data['name'],
            'prefix' => $data['prefix'],
            'updated_by' => $user->id,
        ];

        return $this->repository->update($categoryItem,$newCategoryItem);
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

    public function findById(int $id)
    {
        $data = $this->repository->findById($id);

        if(!$data) {
            throw new HttpResponseException(response()->json([
                "error" => "CATEGORY_ITEM_NOT_FOUND"
            ])->setStatusCode(404));
        }

        return $data;
    }

    public function getCategoryItemId($id)
    {
        $data = $this->repository->findById($id);

        if(!$data) {
            throw new HttpResponseException(response()->json([
                "error" => "CATEGORY_ITEM_NOT_FOUND"
            ])->setStatusCode(404));
        }

        return $data;
    }

    public function validateCategoryName(string $name)
    {
        $data =  $this->repository->validateCategoryNameExists(($name));

        if($data) {
            throw new HttpResponseException(response()->json( [
                "error" => "NAME_EXISTS"
            ])->setStatusCode(400));
        }

        return true;
    }

    public function validateCategoryMasterItem($id)
    {

        $data =  $this->repository->validateCategoryMasterItem($id);
        if($data) {
            throw new HttpResponseException(response()->json( [
                "error" => "CATEGORY_EXIST_IN_ITEMS"
            ])->setStatusCode(400));
        }

        return true;
    }

    public function validateCategoryItemPrefix($prefix)
    {
        $data = $this->repository->validateCategoryItemPrefix($prefix);
        if($data) {
            throw new HttpResponseException(response()->json([
                'error' => 'PREFIX_EXISTS',
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