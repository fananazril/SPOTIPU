<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['user_id'])) {
    header("Location: /SPOTIPU/login/login.php"); 
    exit;
}
$currentUserId = $_SESSION['user_id']; 
require_once __DIR__ . '/../../config/koneksi_db.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $judul = trim($_POST['judul'] ?? '');
    $artist = trim($_POST['artist'] ?? '');
    $genre = trim($_POST['genre'] ?? '');
    $tahun = filter_input(INPUT_POST, 'tahun', FILTER_VALIDATE_INT, ["options" => ["min_range"=>1900, "max_range"=>date('Y')]]);
    if (empty($judul) || empty($artist)) {
    } else {
        if ($tahun === false || $tahun === null) { $tahun = null; }
        $sql = "INSERT INTO lagu (judul, artist, genre, tahun, userid) VALUES (?, ?, ?, ?, ?)"; 
        $stmt = mysqli_prepare($conn, $sql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "sssii", $judul, $artist, $genre, $tahun, $currentUserId); 

            if (mysqli_stmt_execute($stmt)) {
            } else {
                error_log("Error execute statement: " . mysqli_stmt_error($stmt));
            }
            mysqli_stmt_close($stmt);
        } else {
            error_log("Error prepare statement: " . mysqli_error($conn));
        }
    }
}
if ($conn) { mysqli_close($conn); }
header("Location: /SPOTIPU/beranda/home.php");
exit;
?>