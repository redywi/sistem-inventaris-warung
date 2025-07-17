<?php
// File: api/api_barang.php
// Endpoint tunggal untuk semua operasi CRUD pada sumber daya 'barang'.

// Mengatur header untuk memastikan respons adalah JSON dan dapat diakses dari mana saja (CORS)
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once '../config/database.php';

// Mendapatkan metode request HTTP
$method = $_SERVER['REQUEST_METHOD'];

// Menangani request OPTIONS (pre-flight request dari browser untuk CORS)
if ($method == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Mengambil data dari body request untuk POST, PUT, DELETE
$data = json_decode(file_get_contents("php://input"));

switch ($method) {
    case 'GET':
        // Logika untuk membaca data (Read)
        $sql = "SELECT b.id_barang, b.nama_barang, k.nama_kategori, b.harga_beli, b.harga_jual, b.stok 
                FROM tabel_barang b 
                LEFT JOIN tabel_kategori k ON b.id_kategori = k.id_kategori";

        if (isset($_GET['id'])) {
            $id = filter_var($_GET['id'], FILTER_VALIDATE_INT);
            if ($id === false) {
                http_response_code(400);
                echo json_encode([]); // perbaikan di sini
                exit();
            }
            $sql.= " WHERE b.id_barang =?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);
            $result = $stmt->fetch();
        } else {
            $sql.= " ORDER BY b.nama_barang ASC";
            $stmt = $pdo->query($sql);
            $result = $stmt->fetchAll();
        }

        if ($result) {
            http_response_code(200);
            echo json_encode($result);
        } else {
            http_response_code(404);
            echo json_encode([]); // perbaikan di sini
        }
        break;

    case 'POST':
        // Logika untuk membuat data baru (Create)
        if (
           !empty($data->nama_barang) &&
           !empty($data->id_kategori) &&
            isset($data->harga_beli) &&
            isset($data->harga_jual) &&
            isset($data->stok)
        ) {
            $sql = "INSERT INTO tabel_barang (nama_barang, id_kategori, harga_beli, harga_jual, stok) VALUES (?,?,?,?,?)";
            $stmt = $pdo->prepare($sql);

            try {
                if ($stmt->execute([$data->nama_barang, $data->id_kategori, $data->harga_beli, $data->harga_jual, $data->stok])) {
                    $last_id = $pdo->lastInsertId();
                    http_response_code(201); // Created
                    echo json_encode(['id' => $last_id]); // perbaikan di sini
                }
            } catch (PDOException $e) {
                http_response_code(503); // Service Unavailable
                echo json_encode(['message' => 'Gagal membuat barang. Error: '. $e->getMessage()]);
            }
        } else {
            http_response_code(400); // Bad Request
            echo json_encode([]); // perbaikan di sini
        }
        break;

    case 'PUT':
        // Logika untuk memperbarui data (Update)
        if (
           !empty($data->id_barang) &&
           !empty($data->nama_barang) &&
           !empty($data->id_kategori) &&
            isset($data->harga_beli) &&
            isset($data->harga_jual) &&
            isset($data->stok)
        ) {
            $sql = "UPDATE tabel_barang SET nama_barang =?, id_kategori =?, harga_beli =?, harga_jual =?, stok =? WHERE id_barang =?";
            $stmt = $pdo->prepare($sql);

            try {
                if ($stmt->execute([$data->nama_barang, $data->id_kategori, $data->harga_beli, $data->harga_jual, $data->stok, $data->id_barang])) {
                    if ($stmt->rowCount() > 0) {
                        http_response_code(200); // OK
                        echo json_encode(['message' => 'Barang berhasil diperbarui']); // perbaikan di sini
                    } else {
                        http_response_code(404); // Not Found
                        echo json_encode([]); // perbaikan di sini
                    }
                }
            } catch (PDOException $e) {
                http_response_code(503); // Service Unavailable
                echo json_encode(['message' => 'Gagal memperbarui barang. Error: '. $e->getMessage()]);
            }
        } else {
            http_response_code(400); // Bad Request
            echo json_encode([]); // perbaikan di sini
        }
        break;

    case 'DELETE':
        // Logika untuk menghapus data (Delete)
        if (!empty($data->id_barang)) {
            $sql = "DELETE FROM tabel_barang WHERE id_barang =?";
            $stmt = $pdo->prepare($sql);

            try {
                if ($stmt->execute([$data->id_barang])) {
                    if ($stmt->rowCount() > 0) {
                        http_response_code(200); // OK
                        echo json_encode(['message' => 'Barang berhasil dihapus']); // perbaikan di sini
                    } else {
                        http_response_code(404); // Not Found
                        echo json_encode([]); // perbaikan di sini
                    }
                }
            } catch (PDOException $e) {
                http_response_code(503); // Service Unavailable
                echo json_encode(['message' => 'Gagal menghapus barang. Error: '. $e->getMessage()]);
            }
        } else {
            http_response_code(400); // Bad Request
            echo json_encode([]); // perbaikan di sini
        }
        break;

    default:
        // Metode request tidak valid
        http_response_code(405); // Method Not Allowed
        echo json_encode([]); // perbaikan di sini
        break;
}
?>