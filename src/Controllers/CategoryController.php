<?php

declare(strict_types=1);

namespace Swifty\Controllers;

use Swifty\Models\Category;
use Swifty\Config\Database;

/**
 * Controller for handling category-related API requests
 */
class CategoryController extends BaseController
{
    private Category $categoryModel;

    public function __construct()
    {
        $this->setCorsHeaders();
        
        try {
            $database = Database::fromEnv();
            $connection = $database->connect();
            $this->categoryModel = new Category($connection);
        } catch (\Exception $e) {
            $this->handleException($e);
        }
    }

    /**
     * Get all categories
     */
    public function getAllCategories(): void
    {
        try {
            $categories = $this->categoryModel->findAll('id DESC');
            
            if (empty($categories)) {
                $this->sendError("No categories found", 404);
                return;
            }

            $this->sendSuccess($categories);
        } catch (\Exception $e) {
            $this->handleException($e);
        }
    }

    /**
     * Get a single category by ID
     */
    public function getCategoryById(): void
    {
        try {
            $id = $this->getId();
            
            if ($id === null) {
                $this->sendError("Category ID is required", 400);
                return;
            }

            $category = $this->categoryModel->findById($id);
            
            if (!$category) {
                $this->sendError("Category not found", 404);
                return;
            }

            $this->sendSuccess($category);
        } catch (\Exception $e) {
            $this->handleException($e);
        }
    }

    /**
     * Create a new category
     */
    public function createCategory(): void
    {
        try {
            $data = $this->getJsonInput();
            
            if (!$data) {
                $this->sendError("JSON data is required", 400);
                return;
            }

            $categoryId = $this->categoryModel->create($data);
            
            $this->sendSuccess([
                'message' => 'Category created successfully',
                'id' => $categoryId
            ], 201);
            
        } catch (\Exception $e) {
            $this->handleException($e);
        }
    }

    /**
     * Update an existing category
     */
    public function updateCategory(): void
    {
        try {
            $id = $this->getId();
            
            if ($id === null) {
                $this->sendError("Category ID is required", 400);
                return;
            }

            $data = $this->getJsonInput();
            
            if (!$data) {
                $this->sendError("JSON data is required", 400);
                return;
            }

            $this->categoryModel->update($id, $data);
            
            $this->sendSuccess([
                'message' => 'Category updated successfully'
            ]);
            
        } catch (\Exception $e) {
            $this->handleException($e);
        }
    }

    /**
     * Delete a category
     */
    public function deleteCategory(): void
    {
        try {
            $id = $this->getId();
            
            if ($id === null) {
                $this->sendError("Category ID is required", 400);
                return;
            }

            $this->categoryModel->deleteById($id);
            
            $this->sendSuccess([
                'message' => '✅ Category deleted successfully!'
            ]);
            
        } catch (\Exception $e) {
            $this->handleException($e);
        }
    }
}