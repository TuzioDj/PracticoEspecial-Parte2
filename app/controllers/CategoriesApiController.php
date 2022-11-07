<?php
require_once './app/models/CategoriesModel.php';
require_once './app/views/JSONView.php';


class CategoriesApiController
{

    private $model;
    private $view;


    public function __construct()
    {
        $this->model = new CategoriesModel();
        $this->view = new JSONView();
    }
}