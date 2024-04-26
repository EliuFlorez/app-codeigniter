<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\API\ResponseTrait;
use App\Models\Blog;

class Home extends BaseController
{
    use ResponseTrait;
    
    protected $model;
    protected $format = 'json';

    public function __construct()
    {
        $this->model = new Blog();
    }

    public function index(): string
    {
        $searchTerm = $this->request->getVar('search');
        $page = $this->request->getVar('page') ?? 1;

        $data = [
            "blogs" => [],
            "authors" => [],
        ];

        if ($searchTerm !== null) {
            $data['blogs'] = $this->model->like('title', $searchTerm)
                                ->orLike('author', $searchTerm)
                                ->orLike('content', $searchTerm)
                                ->findAll();
        } else {
            $data['blogs'] = $this->model->findAll();
        }

        $data['authors'] = $this->model->distinct()
                               ->select('author')
                               ->findAll();

        return view('home', $data);
    }
}
