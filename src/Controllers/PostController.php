<?php

declare(strict_types=1);

namespace Swifty\Controllers;

use Swifty\Models\Post;
use Swifty\Config\Database;

/**
 * Controller for handling post-related API requests
 */
class PostController extends BaseController
{
    private Post $postModel;

    public function __construct()
    {
        $this->setCorsHeaders();
        
        try {
            $database = Database::fromEnv();
            $connection = $database->connect();
            $this->postModel = new Post($connection);
        } catch (\Exception $e) {
            $this->handleException($e);
        }
    }

    /**
     * Get all posts
     */
    public function getAllPosts(): void
    {
        try {
            $posts = $this->postModel->getAllWithCategory();
            
            if (empty($posts)) {
                $this->sendError("No posts found", 404);
                return;
            }

            // Process posts to decode HTML entities in body
            $processedPosts = array_map(function($post) {
                $post['body'] = html_entity_decode($post['body']);
                return $post;
            }, $posts);

            $this->sendSuccess($processedPosts);
        } catch (\Exception $e) {
            $this->handleException($e);
        }
    }

    /**
     * Get a single post by ID
     */
    public function getPostById(): void
    {
        try {
            $id = $this->getId();
            
            if ($id === null) {
                $this->sendError("Post ID is required", 400);
                return;
            }

            $post = $this->postModel->getByIdWithCategory($id);
            
            if (!$post) {
                $this->sendError("Post not found", 404);
                return;
            }

            // Decode HTML entities in body
            $post['body'] = html_entity_decode($post['body']);

            $this->sendSuccess($post);
        } catch (\Exception $e) {
            $this->handleException($e);
        }
    }

    /**
     * Create a new post
     */
    public function createPost(): void
    {
        try {
            $data = $this->getJsonInput();
            
            if (!$data) {
                $this->sendError("JSON data is required", 400);
                return;
            }

            $postId = $this->postModel->create($data);
            
            $this->sendSuccess([
                'message' => 'Post created successfully',
                'id' => $postId
            ], 201);
            
        } catch (\Exception $e) {
            $this->handleException($e);
        }
    }

    /**
     * Update an existing post
     */
    public function updatePost(): void
    {
        try {
            $id = $this->getId();
            
            if ($id === null) {
                $this->sendError("Post ID is required", 400);
                return;
            }

            $data = $this->getJsonInput();
            
            if (!$data) {
                $this->sendError("JSON data is required", 400);
                return;
            }

            $this->postModel->update($id, $data);
            
            $this->sendSuccess([
                'message' => 'Post updated successfully'
            ]);
            
        } catch (\Exception $e) {
            $this->handleException($e);
        }
    }

    /**
     * Delete a post
     */
    public function deletePost(): void
    {
        try {
            $id = $this->getId();
            
            if ($id === null) {
                $this->sendError("Post ID is required", 400);
                return;
            }

            // Check if post exists before deletion
            if (!$this->postModel->findById($id)) {
                $this->sendError("Post not found", 404);
                return;
            }

            $this->postModel->deleteById($id);
            
            $this->sendSuccess([
                'message' => '✅ Post deleted successfully!'
            ]);
            
        } catch (\Exception $e) {
            $this->handleException($e);
        }
    }
}