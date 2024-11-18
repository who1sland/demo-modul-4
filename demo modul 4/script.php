<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$method = $_SERVER['REQUEST_METHOD'];

// Simulated data storage (use a real database for production applications)
$dataFile = 'data.json';

// Function to read data from the file
function readData($file) {
    return file_exists($file) ? json_decode(file_get_contents($file), true) : [];
}

// Function to write data to the file
function writeData($file, $data) {
    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
}

$data = readData($dataFile);

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $item = array_filter($data, fn($d) => $d['id'] == $id);
            echo json_encode(array_values($item));
        } else {
            echo json_encode($data);
        }
        break;

    case 'POST':
        $input = json_decode(file_get_contents("php://input"), true);
        if ($input) {
            // Get the data from the form (product order)
            $newItem = [
                "id" => uniqid(),
                "name" => $input['name'] ?? '',
                "address" => $input['address'] ?? '',
                "product" => $input['product'] ?? '',
                "quantity" => $input['quantity'] ?? '',
                "contact" => $input['contact'] ?? ''
            ];
            $data[] = $newItem;
            writeData($dataFile, $data);
            echo json_encode(["status" => "success", "message" => "Order successfully placed"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Invalid data"]);
        }
        break;

    case 'PUT':
        $input = json_decode(file_get_contents("php://input"), true);
        if ($input && isset($input['id'])) {
            $updated = false;
            foreach ($data as &$item) {
                if ($item['id'] == $input['id']) {
                    $item['name'] = $input['name'] ?? $item['name'];
                    $item['address'] = $input['address'] ?? $item['address'];
                    $item['product'] = $input['product'] ?? $item['product'];
                    $item['quantity'] = $input['quantity'] ?? $item['quantity'];
                    $item['contact'] = $input['contact'] ?? $item['contact'];
                    $updated = true;
                    break;
                }
            }
            if ($updated) {
                writeData($dataFile, $data);
                echo json_encode(["status" => "success", "message" => "Data successfully updated"]);
            } else {
                echo json_encode(["status" => "error", "message" => "Data not found"]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Invalid ID"]);
        }
        break;

        case 'DELETE':
            // Menghapus data berdasarkan ID
            if (isset($_GET['id'])) {
                $id = $_GET['id']; // Mengambil ID dari parameter URL
                $originalCount = count($data); // Menyimpan jumlah data sebelum penghapusan
    
                // Menghapus produk berdasarkan ID
                $data = array_filter($data, fn($item) => $item['id'] != $id);
    
                // Jika ada data yang dihapus, simpan perubahan ke file
                if (count($data) < $originalCount) {
                    // Menyimpan data yang telah diperbarui setelah penghapusan
                    writeData($dataFile, $data);
                    echo json_encode(["status" => "success", "message" => "Produk berhasil dihapus"]);
                } else {
                    echo json_encode(["status" => "error", "message" => "Produk dengan ID tersebut tidak ditemukan"]);
                }
            } else {
                echo json_encode(["status" => "error", "message" => "ID produk tidak ditemukan"]);
            }
            break;
    
        default:
            echo json_encode(["status" => "error", "message" => "Metode tidak didukung"]);
            break;
}
?>
