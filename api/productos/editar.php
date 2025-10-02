<?php
require_once '../includes/secure_api.php';


$isFormData = strpos($_SERVER['CONTENT_TYPE'] ?? '', 'multipart/form-data') !== false;

if ($isFormData) {

    $id = $_POST['id'] ?? null;
    $nombre = trim($_POST['nombre'] ?? '');
    $precio = floatval($_POST['precio'] ?? 0);
    $id_categoria = $_POST['id_categoria'] ?? null;
    $stock = intval($_POST['stock'] ?? 0);
    $stock_minimo = intval($_POST['stock_minimo'] ?? 0);
    $descripcion = trim($_POST['descripcion'] ?? '');
    $activo = $_POST['activo'] ?? 1;


    $imagen = null;
    $nuevaImagen = false;

    if (isset($_FILES['imagen_archivo']) && $_FILES['imagen_archivo']['error'] === UPLOAD_ERR_OK) {
        $directorioImagenes = '../../uploads/productos/';

        // Crear directorio si no existe
        if (!is_dir($directorioImagenes)) {
            mkdir($directorioImagenes, 0777, true);
        }

        // Validar tipo de archivo
        $tiposPermitidos = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $tipoArchivo = $_FILES['imagen_archivo']['type'];

        if (!in_array($tipoArchivo, $tiposPermitidos)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Tipo de archivo no permitido. Solo se permiten JPG, PNG, GIF y WebP.']);
            exit;
        }

        // Validar tamaño (2MB máximo)
        if ($_FILES['imagen_archivo']['size'] > 2 * 1024 * 1024) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'La imagen no debe superar los 2MB.']);
            exit;
        }

        // Obtener imagen actual para eliminarla después
        $sqlActual = "SELECT imagen FROM productos WHERE id = ?";
        $stmtActual = $pdo->prepare($sqlActual);
        $stmtActual->execute([$id]);
        $productoActual = $stmtActual->fetch(PDO::FETCH_ASSOC);

        // Eliminar imagen anterior si existe
        if ($productoActual && $productoActual['imagen'] && file_exists('../' . $productoActual['imagen'])) {
            unlink('../' . $productoActual['imagen']);
        }

        // Generar nombre único para la nueva imagen
        $extension = pathinfo($_FILES['imagen_archivo']['name'], PATHINFO_EXTENSION);
        $nombreImagen = uniqid() . '_' . time() . '.' . $extension;
        $rutaImagen = $directorioImagenes . $nombreImagen;

        // Mover archivo
        if (move_uploaded_file($_FILES['imagen_archivo']['tmp_name'], $rutaImagen)) {
            $imagen = 'uploads/productos/' . $nombreImagen;
            $nuevaImagen = true;
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error al subir la imagen.']);
            exit;
        }
    }
} else {
    // Procesar JSON normal (compatibilidad con versión anterior)
    $data = json_decode(file_get_contents("php://input"), true);

    $id = $data['id'] ?? null;
    $nombre = trim($data['nombre'] ?? '');
    $precio = floatval($data['precio'] ?? 0);
    $id_categoria = $data['id_categoria'] ?? null;
    $stock = intval($data['stock'] ?? 0);
    $stock_minimo = intval($data['stock_minimo'] ?? 0);
    $descripcion = trim($data['descripcion'] ?? '');
    $activo = $data['activo'] ?? 1;
    $nuevaImagen = false;
}

// Validaciones comunes
if (!$id) {
    http_response_code(400);
    echo json_encode(["error" => "ID del producto es requerido."]);
    exit;
}

if (!$nombre || $precio <= 0 || !$id_categoria) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Nombre, precio y categoría son requeridos.']);
    exit;
}

try {
    // Actualizar producto con o sin nueva imagen
    if ($nuevaImagen) {
        $sql = "UPDATE productos SET nombre = ?, precio = ?, descripcion = ?, id_categoria = ?, stock = ?, stock_minimo = ?, activo = ?, imagen = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $success = $stmt->execute([$nombre, $precio, $descripcion, $id_categoria, $stock, $stock_minimo, $activo, $imagen, $id]);
    } else {
        $sql = "UPDATE productos SET nombre = ?, precio = ?, descripcion = ?, id_categoria = ?, stock = ?, stock_minimo = ?, activo = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $success = $stmt->execute([$nombre, $precio, $descripcion, $id_categoria, $stock, $stock_minimo, $activo, $id]);
    }

    echo json_encode(["success" => $success, "message" => "Producto actualizado correctamente"]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Error al editar producto", "detalle" => $e->getMessage()]);
}
