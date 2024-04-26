<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\User;

class Users extends ResourceController
{
    use ResponseTrait;
    
    protected $model;
    protected $format = 'json';

    public function __construct()
    {
        $this->model = new User();
    }

    /**
     * Return an array of resource objects, themselves in array format.
     *
     * @return ResponseInterface
     */
    public function index()
    {
        return $this->respond($this->model->findAll());
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
            'name' => 'required',
        ];
        
        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $data = [
            'name' => $this->request->getVar('name'),
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
            'name' => 'required',
        ];
        
        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $data = [
            'name' => $this->request->getVar('name'),
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
