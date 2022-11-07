<?php
require_once './app/models/ProductsModel.php';
require_once './app/views/JSONView.php';


class ProductsApiController
{

    private $model;
    private $view;
    private $data;


    public function __construct()
    {
        $this->model = new ProductsModel();
        $this->view = new JSONView();
        $this->data = file_get_contents("php://input");
    }

    public function getAllProducts($params = NULL)
    {
        $defaultSortBy = "idProducto";
        $defaultOrder = "asc";
        $size_pages = 10;
        $page = 1;
        if (isset($_GET["limit"])) {
            $size_pages = $this->ConvertNatural($_GET["limit"], $size_pages);
        }
        if (isset($_GET["page"])) {
            $page = $this->ConvertNatural($_GET["page"], $page);
        }

        $start_where = ($page - 1) * $size_pages;

        try {
            if (!empty($_GET["sortBy"]) && !empty($_GET["order"])) {
                $products = $this->model->getAllProducts($start_where, $size_pages, $_GET["sortBy"], $_GET["order"]);
            } 
            else if (!empty($_GET["sortBy"])) {
                $products = $this->model->getAllProducts($start_where, $size_pages, $_GET["sortBy"], $defaultOrder);
            } 
            else if (!empty($_GET["order"])) {
                $products = $this->model->getAllProducts($start_where, $size_pages, $defaultSortBy, $_GET["order"]);
            } 
            else {
                $products = $this->model->getAllProducts($start_where, $size_pages);
            }
        } catch (Exception) {
            $this->view->response("El servidor no pudo interpretar la solicitud dada una sintaxis invalida", 400);
        }
        if (isset($products)) {
            $this->view->response($products, 200);
        }
    }
    public function getProduct($params = NULL)
    {
        $id = $params[":ID"];
        $product = $this->model->getProduct($id);
        if ($product) {
            $this->view->response($product, 200);
        } else {
            $this->view->response("No existe un producto con el id {$id}", 404);
        }
    }

    public function deleteProduct($params = NULL)
    {
        $id = $params[':ID'];
        $product = $this->model->getProduct($id);

        if ($product) {
            $this->model->deleteProduct($id);
            $this->view->response("El producto $id fue eliminado correctamente", 200);
        } else
            $this->view->response("No existe un producto con el id {$id}", 404);
    }

    public function addProduct($params = NULL)
    {
        $data = $this->getData();
        try {
            $id = $this->model->addProduct($data->nombre, $data->precio, $data->descripcion, $data->imagen, $data->categoria);
            $this->view->response("El producto $id fue añadido correctamente", 200);
        } catch (Exception) {
            $this->view->response("El servidor no pudo interpretar la solicitud dada una sintaxis invalida", 400);
        }
    }

    public function editProduct($params = NULL)
    {
        $data = $this->getData();
        try {
            if ($data->imagen) {
                $id = $this->model->editProduct($data->nombre, $data->precio, $data->descripcion, $data->categoria, $data->id, $data->imagen);
            }
            else {
                $id = $this->model->editProduct($data->nombre, $data->precio, $data->descripcion, $data->categoria, $data->id);
            }
            $this->view->response("El producto $id fue modificado correctamente", 200);
        } catch (Exception) {
            $this->view->response("El servidor no pudo interpretar la solicitud dada una sintaxis invalida", 400);
        }
    }



    public function getData()
    {
        return json_decode($this->data);
    }

    function ConvertNatural($param, $defaultParam)
    {
        $result = intval($param);
        if ($result > 0) {
            $result = $param;
        } else {
            $result = $defaultParam;
        }
        return $result;
    }
}
