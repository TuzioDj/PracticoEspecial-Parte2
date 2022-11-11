<?php

class ProductsModel
{

    private $db;

    public function __construct()
    {
        $this->db = new PDO('mysql:host=localhost;' . 'dbname=practicoespecial;charset=utf8', 'root', '');
    }
    // TRAIGO TODOS LOS PRODUCTOS CON SUS RESPECTIVAS CATEGORIAS (FUSIONO TABLAS)
    function getProducts($inicioPag, $tamanioPag, $sortedby = "idProducto", $order = "asc")
    {
        $query = $this->db->prepare("SELECT a.*, b.* 
                                                             FROM productos a 
                                                             INNER JOIN tipodeproductos b 
                                                             ON a.idTipoDeProducto = b.idTipo
                                                             ORDER BY $sortedby $order
                                                             LIMIT $inicioPag,$tamanioPag");
        $query->execute();
        $products = $query->fetchAll(PDO::FETCH_OBJ);

        return $products;
    }
    
    function getFiltredProducts($inicioPag, $tamanioPag, $filter,$search)
    {
        $query = $this->db->prepare("SELECT * FROM productos
                                                              WHERE $filter = ?
                                                              LIMIT $inicioPag,$tamanioPag");
        $query->execute([$search]);

        $products = $query->fetchAll(PDO::FETCH_OBJ);

        return $products;
    }
    
    // TRAIGO UN PRODUCTO POR ID
    function getProduct($id)
    {
        $query = $this->db->prepare("SELECT a.*, b.* 
                                                             FROM productos a 
                                                             INNER JOIN tipodeproductos b 
                                                             ON a.idTipoDeProducto = b.idTipo 
                                                             WHERE idProducto = ?");
        $query->execute([$id]);

        $product = $query->fetch(PDO::FETCH_OBJ);

        return $product;
    }


    // AÑADO UN PRODUCTO
    function addProduct($nombre, $precio, $descripcion, $imagen, $categoria)
    {
        // SUBO LA IMAGEN AL TARGET Y RECIBO EL MISMO
        $pathImg = $this->uploadImage($imagen);


        $query = $this->db->prepare("INSERT INTO `productos` (`nombre`, `precio`, `descripcion`, `imagen`, `idTipoDeProducto`, `idProducto`) 
                                                             VALUES (?, ?, ?, ?, ?, ?);");
        $query->execute([$nombre, $precio, $descripcion, $pathImg, $categoria, NULL]);

        return $this->db->lastInsertId();
    }
    // EDITO UN PRODUCTO. SI VIENE UNA IMAGEN LA REEMPLAZA POR LA QUE ESTA. SI NO VIENE IMAGEN REEMPLAZA EL RESTO DE DATOS
    function editProduct($nombre, $precio, $descripcion, $categoria, $id, $imagen = null)
    {
        if ($imagen) {
            // SUBO LA IMAGEN AL TARGET Y RECIBO EL MISMO
            $pathImg = $this->uploadImage($imagen);
            $query = $this->db->prepare("UPDATE `productos` 
                                                                 SET `nombre` = ?, `precio` = ?, `descripcion` = ?, `imagen` = ?, `idTipoDeProducto` = ? 
                                                                 WHERE `productos`.`idProducto` = ?");
            $query->execute([$nombre, $precio, $descripcion, $pathImg, $categoria, $id]);
        } else {
            $query = $this->db->prepare("UPDATE `productos` 
                                                                 SET `nombre` = ?, `precio` = ?, `descripcion` = ?, `idTipoDeProducto` = ? 
                                                                 WHERE `productos`.`idProducto` = ?");
            $query->execute([$nombre, $precio, $descripcion, $categoria, $id]);
        }
    }
    // BORRO PRODUCTO POR ID
    function deleteProduct($id)
    {
        $query = $this->db->prepare("DELETE FROM `productos` 
                                                             WHERE `productos`.`idProducto` = ?");
        $query->execute([$id]);
    }


    function getAllColumns(){
        $query = $this->db->prepare("SELECT COLUMN_NAME 
                                                             FROM INFORMATION_SCHEMA.COLUMNS 
                                                             WHERE TABLE_NAME = N'productos'");
        $query->execute();

        $columns = $query->fetchAll(PDO::FETCH_OBJ);

        return $columns;
    }
    private function uploadImage($image)
    {
        $target = 'img/product/' . uniqid() . '.jpg';
        move_uploaded_file($image, $target);
        return $target;
    }
}
