<?php
require_once './app/models/ProductsModel.php';
require_once './app/views/JSONView.php';
require_once './app/helpers/AuthHelper.php';


class ProductsApiController
{
    private $model;
    private $view;
    private $authHelper;

    private $data;

    public function __construct()
    {
        $this->model = new ProductsModel();
        $this->view = new JSONView();
        $this->authHelper = new AuthHelper();

        $this->data = file_get_contents("php://input");
    }

    public function getProducts($params = NULL)
    {
        $defaultSortBy = "idProducto";
        $defaultOrder = "asc";
        $pagSize = 10;
        $page = 1;
        if (isset($_GET["limit"])) {
            $pagSize = $this->ConvertNatural($_GET["limit"], $pagSize);
        }
        if (isset($_GET["page"])) {
            $page = $this->ConvertNatural($_GET["page"], $page);
        }
        if (isset($_GET["sortBy"])) {
            $sortBy = $this->sanitizeWithTable($_GET["sortBy"]);
        }
        if (isset($_GET["filter"])) {
            $filter = $this->sanitizeWithTable($_GET["filter"]);
        }

        $startWherePag = ($page - 1) * $pagSize;

        try {
            if (!empty($filter) && isset($_GET["search"]) && !empty($_GET["order"]) && !empty($sortBy)) {
                $products = $this->model->getFiltredProducts($startWherePag, $pagSize, $filter, $_GET["search"], $sortBy, $_GET["order"]);
            } 
            else if (!empty($filter) && isset($_GET["search"]) && !empty($_GET["order"])) {
                $products = $this->model->getFiltredProducts($startWherePag, $pagSize, $filter, $_GET["search"], $defaultSortBy, $_GET["order"]);
            } 
            else if (!empty($filter) && isset($_GET["search"]) && !empty($sortBy)) {
                $products = $this->model->getFiltredProducts($startWherePag, $pagSize, $filter, $_GET["search"], $sortBy, $defaultOrder);
            } 
            else if (!empty($filter) && isset($_GET["search"])) {
                $products = $this->model->getFiltredProducts($startWherePag, $pagSize, $filter, $_GET["search"], $defaultSortBy, $defaultOrder);
            } 
            else if (!empty($sortBy) && !empty($_GET["order"])) {
                $products = $this->model->getProducts($startWherePag, $pagSize, $sortBy, $_GET["order"]);
            } 
            else if (!empty($sortBy)) {
                $products = $this->model->getProducts($startWherePag, $pagSize, $sortBy, $defaultOrder);
            } 
            else if (!empty($_GET["order"])) {
                $products = $this->model->getProducts($startWherePag, $pagSize, $defaultSortBy, $_GET["order"]);
            } 
            else {
                $products = $this->model->getProducts($startWherePag, $pagSize);
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
        if (!$this->authHelper->isLoggedIn()) {
            $this->view->response("Necesitas loguearte para poder realizar esta accion", 401);
            return;
        }
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
        if (!$this->authHelper->isLoggedIn()) {
            $this->view->response("Necesitas loguearte para poder realizar esta accion", 401);
            return;
        }
        $data = $this->getData();
        try {
            if ($this->existDataInTable($this->getData())) {
                if (isset($data->imagen)) {
                    $id = $this->model->addProduct($data->nombre, $data->precio, $data->descripcion, $data->imagen, $data->idTipoDeProducto);
                    $this->view->response("El producto $id fue añadido correctamente", 201);
                } else {
                    $this->view->response("Falta colocar una imagen", 400);
                }
            } else {
                $this->view->response("Falta llenar algun campo", 400);
            }
        } catch (Exception) {
            $this->view->response("El servidor no pudo interpretar la solicitud dada una sintaxis invalida", 400);
        }
    }

    public function editProduct($params = NULL)
    {
        if (!$this->authHelper->isLoggedIn()) {
            $this->view->response("Necesitas loguearte para poder realizar esta accion", 401);
            return;
        }
        $id = $params[':ID'];
        $producto = $this->model->getProduct($id);
        if ($producto) {
            $data = $this->getData();
            try {
                if ($this->existDataInTable($data)) {
                    if ($data->imagen) {
                        $id = $this->model->editProduct($data->nombre, $data->precio, $data->descripcion, $data->idTipoDeProducto, $id, $data->imagen);
                    } else {
                        $id = $this->model->editProduct($data->nombre, $data->precio, $data->descripcion, $data->categoria, $id);
                    }
                    $this->view->response("El producto $id fue modificado correctamente", 200);
                } else {
                    $this->view->response("Falta llenar algun campo", 400);
                }
            } catch (Exception) {
                $this->view->response("El servidor no pudo interpretar la solicitud dada una sintaxis invalida", 400);
            }
        } else {
            $this->view->response("El producto que quieres editar no existe", 404);
        }
    }



    public function getData()
    {
        return json_decode($this->data);
    }

    function ConvertNatural($param, $defaultParam)
    {
        $result = intval($param);
        if ($result >= 0) {
            $result = $param;
        } else {
            $result = $defaultParam;
        }
        return $result;
    }

    function sanitizeWithTable($param)
    {
        $columns = $this->model->getAllColumns();
        for ($i = 0; $i < sizeof($columns); $i++) {
            $aux = $columns[$i]->COLUMN_NAME;
            if ($aux == $param) {
                return $param;
            }
        }
        return null;
    }

    function existDataInTable($param)
    {
        $param->idProducto = "skipped";
        $param->imagen = "skipped";
        $columns = $this->model->getAllColumns();
        for ($i = 0; $i < sizeof($columns); $i++) {
            $aux = $columns[$i]->COLUMN_NAME;
            if (empty($param->$aux)) {
                return false;
            }
        }
        return true;
    }

    public function pageNotFound()
    {
        $this->view->response("Pagina no encontrada", 404);
    }
}
