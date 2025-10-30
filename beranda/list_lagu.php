<?php
function getAllLagu(mysqli $connection, int $userid, string $sortBy = 'judul', string $sortOrder = 'ASC'): array {
    $allLagu = [];
    $allowedCols = ['id','judul', 'artist', 'genre', 'tahun'];
    $allowedOrders = ['ASC', 'DESC'];
    $sortBy = in_array($sortBy, $allowedCols) ? $sortBy : 'judul';
    $sortOrder = in_array(strtoupper($sortOrder), $allowedOrders) ? strtoupper($sortOrder) : 'ASC';
    $sql = "SELECT id, judul, artist, genre, tahun FROM lagu WHERE userid = ? ORDER BY `$sortBy` $sortOrder";
    $stmt = mysqli_prepare($connection, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $userid); 
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            if ($result && mysqli_num_rows($result) > 0) {
                $allLagu = mysqli_fetch_all($result, MYSQLI_ASSOC);
            }
            if($result) mysqli_free_result($result);
        } else {
            error_log("Error execute getAllLagu statement: " . mysqli_stmt_error($stmt));
        }
        mysqli_stmt_close($stmt);
    } else {
        error_log("Error prepare getAllLagu statement: " . mysqli_error($connection));
    }
    return $allLagu;
}
?>