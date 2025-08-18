<?php
require_once '../includes/secure_api.php';

$sql = "SELECT 
            p.id,
            p.nombre,
            p.precio,
            p.stock,
            p.stock_minimo,
            p.descripcion,
            p.activo,
            p.fecha_registro,
            p.id_categoria,
            c.nombre AS categoria
        FROM productos p
        INNER JOIN categorias c ON p.id_categoria = c.id
        WHERE p.activo = 1
        ORDER BY p.nombre ASC";

$stmt = $pdo->query($sql);
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($productos);