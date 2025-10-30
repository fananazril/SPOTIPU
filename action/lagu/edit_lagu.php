<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['user_id'])) {
    header("Location: /SPOTIPU/login/login.php");
    exit;
}
$currentUserId = $_SESSION['user_id'];
require_once __DIR__ . '/../../config/koneksi_db.php'; 
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_lagu']) && filter_var($_POST['id_lagu'], FILTER_VALIDATE_INT)) {
    $idLagu = $_POST['id_lagu'];
    $judul = trim($_POST['judul'] ?? '');
    $artist = trim($_POST['artist'] ?? '');
    $genre = trim($_POST['genre'] ?? '');
    $tahun = filter_input(INPUT_POST, 'tahun', FILTER_VALIDATE_INT, ["options" => ["min_range"=>1900, "max_range"=>date('Y')]]);
    if (empty($judul) || empty($artist)) {
    } else {
        if ($tahun === false || $tahun === null) { $tahun = null; }
        $sql = "UPDATE lagu SET judul = ?, artist = ?, genre = ?, tahun = ? WHERE id = ? AND userid = ?"; 
        $stmt = mysqli_prepare($conn, $sql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "sssiii", $judul, $artist, $genre, $tahun, $idLagu, $currentUserId); 
            if (mysqli_stmt_execute($stmt)) {

            } else {
                error_log("Error execute update statement: " . mysqli_stmt_error($stmt));
            }
            mysqli_stmt_close($stmt);
        } else {
            error_log("Error prepare update statement: " . mysqli_error($conn));
        }
    }
}
if ($conn) { mysqli_close($conn); }
header("Location: /SPOTIPU/beranda/home.php");
exit;
?>