<?php

namespace App\Repositories;

use App\Models\CategoryItem;

class CategoryItemRepository
{
    
    public function create($data)
    {
        return CategoryItem::create($data);
    }

    public function update(CategoryItem $categoryItem, $data)
    {
        $categoryItem->fill($data);
        $categoryItem->save();
        return $categoryItem;
    }

    public function delete($categoryItem)
    {
        $categoryItem->delete();
    }

    public function findById(int $id)
    {
        return CategoryItem::find($id);
    }

    public function validateCategoryNameExists(string $name): bool
    {
        return CategoryItem::where("name", $name)->exists();
    }

    public function validateCategoryMasterItem($id)
    {
        return CategoryItem::has('masterItem')->where('id', $id)->exists();
    }

    public function validateCategoryItemPrefix($prefix)
    {
        return CategoryItem::where('prefix', $prefix)->exists();
    }

    public function getAllCategoryItem()
    {
        return CategoryItem::query()
                    ->orderByDesc('active_flag')
                    ->orderBy('name')
                    ->get();
    }

    public function getActiveCategoryItem()
    {
        return CategoryItem::query()
                    ->where('active_flag', 'Y')
                    ->orderByDesc('active_flag')
                    ->orderBy('name')
                    ->get();
    }

    public function getInactiveCategoryItem()
    {
        return CategoryItem::query()
                    ->where('active_flag', 'N')
                    ->orderByDesc('active_flag')
                    ->orderBy('name')
                    ->get();
    }

    public function getCategoItemByPrefix($prefix)
    {
        return CategoryItem::where('prefix', $prefix);
    }
   
}