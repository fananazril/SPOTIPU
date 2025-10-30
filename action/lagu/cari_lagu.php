<?php
function cariLaguByJudul(mysqli $connection, string $keyword, int $userid, string $sortBy = 'judul', string $sortOrder = 'ASC'): array {
    $foundLagu = [];
    $allowedCols = ['id','judul', 'artist', 'genre', 'tahun'];
    $allowedOrders = ['ASC', 'DESC'];
    $sortBy = in_array($sortBy, $allowedCols) ? $sortBy : 'judul';
    $sortOrder = in_array(strtoupper($sortOrder), $allowedOrders) ? strtoupper($sortOrder) : 'ASC';
    $keywordAman = mysqli_real_escape_string($connection, $keyword);
    $sql = "SELECT id, judul, artist, genre, tahun
            FROM lagu
            WHERE judul LIKE ? AND userid = ? 
            ORDER BY `$sortBy` $sortOrder";
    $stmt = mysqli_prepare($connection, $sql);
    if ($stmt) {
        $searchTerm = "%" . $keywordAman . "%";
        mysqli_stmt_bind_param($stmt, "si", $searchTerm, $userid);
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            if ($result && mysqli_num_rows($result) > 0) {
                $foundLagu = mysqli_fetch_all($result, MYSQLI_ASSOC);
            }
            mysqli_free_result($result);
        } else {
            error_log("Error executing search statement: " . mysqli_stmt_error($stmt));
        }
        mysqli_stmt_close($stmt);
    } else {
        error_log("Error preparing search statement: " . mysqli_error($connection));
    }
    return $foundLagu;
}

?>