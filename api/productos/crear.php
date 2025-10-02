<?php
require_once '../includes/secure_api.php';


$isFormData = strpos($_SERVER['CONTENT_TYPE'] ?? '', 'multipart/form-data') !== false;

if ($isFormData) {

  $nombre = trim($_POST['nombre'] ?? '');
  $precio = floatval($_POST['precio'] ?? 0);
  $id_categoria = $_POST['id_categoria'] ?? null;
  $stock = intval($_POST['stock'] ?? 0);
  $stock_minimo = intval($_POST['stock_minimo'] ?? 0);
  $descripcion = trim($_POST['descripcion'] ?? '');
  $activo = $_POST['activo'] ?? 1;


  $imagen = null;
  if (isset($_FILES['imagen_archivo']) && $_FILES['imagen_archivo']['error'] === UPLOAD_ERR_OK) {
    $directorioImagenes = '../../uploads/productos/';

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

    // Generar nombre único para la imagen
    $extension = pathinfo($_FILES['imagen_archivo']['name'], PATHINFO_EXTENSION);
    $nombreImagen = uniqid() . '_' . time() . '.' . $extension;
    $rutaImagen = $directorioImagenes . $nombreImagen;

    // Mover archivo
    if (move_uploaded_file($_FILES['imagen_archivo']['tmp_name'], $rutaImagen)) {
      $imagen = 'uploads/productos/' . $nombreImagen;
    } else {
      http_response_code(500);
      echo json_encode(['success' => false, 'message' => 'Error al subir la imagen.']);
      exit;
    }
  }
} else {
  // Procesar JSON normal (compatibilidad con versión anterior)
  $data = json_decode(file_get_contents("php://input"), true);

  $nombre = trim($data['nombre'] ?? '');
  $precio = floatval($data['precio'] ?? 0);
  $id_categoria = $data['id_categoria'] ?? null;
  $stock = intval($data['stock'] ?? 0);
  $stock_minimo = intval($data['stock_minimo'] ?? 0);
  $descripcion = trim($data['descripcion'] ?? '');
  $activo = $data['activo'] ?? 1;
  $imagen = null;
}

// Validaciones comunes
if (!$nombre || $precio <= 0 || !$id_categoria) {
  http_response_code(400);
  echo json_encode(['success' => false, 'message' => 'Nombre, precio y categoría son requeridos.']);
  exit;
}

try {
  // Insertar producto con o sin imagen
  if ($imagen) {
    $sql = "INSERT INTO productos (nombre, id_categoria, precio, stock, stock_minimo, descripcion, imagen, activo)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nombre, $id_categoria, $precio, $stock, $stock_minimo, $descripcion, $imagen, $activo]);
  } else {
    $sql = "INSERT INTO productos (nombre, id_categoria, precio, stock, stock_minimo, descripcion, activo)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nombre, $id_categoria, $precio, $stock, $stock_minimo, $descripcion, $activo]);
  }

  echo json_encode(['success' => true, 'message' => 'Producto creado correctamente']);
} catch (Exception $e) {
  http_response_code(500);
  echo json_encode(['success' => false, 'message' => 'Error al crear producto: ' . $e->getMessage()]);
}
