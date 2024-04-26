<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\Blog;

class Blogs extends ResourceController
{
    use ResponseTrait;
    
    protected $model;
    protected $format = 'json';

    public function __construct()
    {
        $this->model = new Blog();
    }

    /**
     * Return an array of resource objects, themselves in array format.
     *
     * @return ResponseInterface
     */
    public function index()
    {
        $searchTerm = $this->request->getVar('search');
        $page = $this->request->getVar('page') ?? 1;

        if ($searchTerm !== null) {
            $data = $this->model->like('title', $searchTerm)
                                ->orLike('author', $searchTerm)
                                ->orLike('content', $searchTerm)
                                ->findAll();
        } else {
            $data = $this->model->findAll();
        }

        $authors = $this->model->distinct()
                               ->select('author')
                               ->findAll();

        return $this->respond([
            "data" => $data, 
            "authors" => $authors
        ]);
    }

    /**
     * Return the properties of a resource object.
     *
     * @param int|string|null $id
     *
     * @return ResponseInterface
     */
    public function show($id = null)
    {
        $data = $this->model->find($id);

        if ($data === null) {
            return $this->failNotFound('Not Found');
        }

        return $this->respond($data);
    }

    /**
     * Create a new resource object, from "posted" parameters.
     *
     * @return ResponseInterface
     */
    public function create()
    {
        $rules = [
            'title' => 'required',
            'author' => 'required',
            'content' => 'required',
        ];
        
        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $data = [
            'title' => $this->request->getVar('title'),
            'author' => $this->request->getVar('author'),
            'content' => $this->request->getVar('content'),
        ];

        try {
            $this->model->insert($data);
        } catch (\Exception $e) {
            return $this->respondCreated(['error' => $e->getMessage()]);
        }

        return $this->respondCreated(['success' => true]);
    }

    /**
     * Add or update a model resource, from "posted" properties.
     *
     * @param int|string|null $id
     *
     * @return ResponseInterface
     */
    public function update($id = null)
    {
        $rules = [
            'title' => 'required',
            'author' => 'required',
            'content' => 'required',
        ];
        
        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $data = [
            'title' => $this->request->getVar('title'),
            'author' => $this->request->getVar('author'),
            'content' => $this->request->getVar('content'),
        ];
    
        try {
            $this->model->update($id, $data);
        } catch (\Exception $e) {
            return $this->respondCreated(['error' => $e->getMessage()]);
        }
        
        return $this->respondCreated(['success' => true]);
    }

    /**
     * Delete the designated resource object from the model.
     *
     * @param int|string|null $id
     *
     * @return ResponseInterface
     */
    public function delete($id = null)
    {
        $this->model->delete($id);
        
        return $this->respondCreated(['success' => true]);
    }
}
