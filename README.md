# Trabajo practico especial - Parte 2
Este es el resultado del trabajo realizado a traves de los contenidos dados en clase, espero que te guste!

# Link base
http://localhost/PracticoEspecial-Parte2-main/api/productos

# Logueo
Email: prueba@gmail.com
Contraseña: 12345

Para poder hacer CUD (Create, Update and Delete), sera necesario estar logueado. Para esto, se debe loguear con email y contraseña en el apartado "Autorization", con el "Type: Basic Auth", y la URL debe ser /getToken:
    -/api/getToken
Como resultado vas a obtener un token. Este token se debe pegar en el "Type: Bearer Token" para poder empezar a hacer CUD.
La duracion del token es de 1h (3600s). Una vez pasado este tiempo se debe obtener repetir el proceso anterior.

# Datos por default (Sirven para entender alguna cosas que se mostraran mas tarde)
- Columna por la cual se ordenan los productos: IdProducto.
- Tipo de orden; ASC (Ascendiente).
- Tamaño de pagina: 10 productos.
- Numero de pagina: 1.

# Columnas de la tabla de productos (db)
- nombre
- precio
- descripcion
- imagen
- idTipodeProducto
- idProducto

## Equivalencia de idTipoDeProducto con las categorias
- 5 = Placas de video
- 6 = Procesadores
- 7 = Memorias RAM
- 8 = Motherboards
- 9 = Almacenamiento
- 10 = Fuentes
- 11 = Gabinetes

# Mostrado de productos (GET)

Al utilizar el link base, se mostraran todos los productos con los valores por defecto.

Para agregar parametro de consulta, se debe agregar signo de interrogacion, nombre de la variable y valor:
    - /productos?sortBy=precio&order=desc
Si todo sale bien, el status code sera "200 OK"

## Paginacion

Para paginar de manera personalizada los productos, se debe utilizar "page" para indicar que pagina queremos ver, y "limit" para indicar cuantos elementos queremos por pagina. Deben ser numeros naturales.

Si alguno no se coloca, o se coloca de manera incorrecta, se utilizara el valor por default:
    - /productos?page=1&limit=10

## Ordenacion

Para ordenar los productos por una columna de la tabla de la base de datos, se debe utilizar "sortBy".
Tambien se le puede agregar el parametro "order", el cual debe ser "ASC"(Ascendiente) o "DESC"(Descendiente).

Si alguno no se coloca o se coloca de manera incorrecta, se utilizara el valor por default:
    - /productos?sortBy=idProducto&order=asc

## Filtrado

Para filtrar los productos es necesario colocar la columna por la cual se quiere filtrar con "filter" junto con "search", donde se colocara lo que se quiera buscar dentro de esa columna:
    - /productos?filter=precio&search=asc

## Combinaciones

Todo lo mencionado anteriormente se puede combinar, se puede filtrar, ordenar y paginar, todo en conjunto:
    - /productos?filter=precio&search=199999&sortBy=idTipoDeProducto&order=desc

## Mostrar un solo producto por id

Para mostrar un producto especifico, es necesario especificar el id luego de "/productos/":
    - /productos/1


# Creacion de producto

Para crear un producto nuevo, es necesario estar logueado. En caso contrario, se devolvera el status code "401 Unauthorized".

Se debe especificar en el body todas las columnas de la tabla de la db, excepto "idProducto", con sus respectivos valores, los cuales no puede ser vacios. No pueden faltar campos. En caso de que algo este mal escrito o falte, el status code sera "400 Bad request"

Si todo sale bien, el status code sera "201 Created"

# Edicion de producto

Para editar un producto, es necesario estar logueado. En caso contrario, se devolvera el status code "401 Unauthorized".

Si queres editar un producto, es necesario especificar su id luego de "/productos/":
    - /productos/1

Se debe especificar en el body todas las columnas de la tabla de la db, excepto "idProducto", con sus respectivos valores, los cuales no puede ser vacios. La imagen es opcional. En caso de que algo este mal escrito o falte, el status code sera "400 Bad request"

Si todo sale bien, el status code sera "200 OK"

# Eliminacion de un producto

Para eliminar un producto, es necesario estar logueado. En caso contrario, se devolvera el status code "401 Unauthorized".

Si queres eliminar un producto, es necesario especificar su id luego de "/productos/":
    - /productos/1

En caso de que el producto exista, este se eliminara y el status code sera "200 OK". En caso contrario, el status code sera "404 Not found".