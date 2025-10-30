<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: /SPOTIPU/login/login.php"); 
    exit;
}
$currentUserId = $_SESSION['user_id'] ?? null;
if (!$currentUserId) {
     header("Location: /SPOTIPU/login/login.php?error=Sesi_tidak_valid");
     exit;
}

require_once __DIR__ . '/../config/koneksi_db.php'; //koneksi database
require_once 'list_lagu.php'; //directory list
require_once __DIR__ . '/../action/lagu/cari_lagu.php';  //directory cari

$allowedSortColumns = ['id','judul', 'artist', 'genre', 'tahun'];
$allowedSortOrders = ['ASC', 'DESC'];

$sortBy = isset($_GET['sort_by']) && in_array($_GET['sort_by'], $allowedSortColumns) ? $_GET['sort_by'] : 'judul';
$sortOrder = isset($_GET['sort_order']) && in_array(strtoupper($_GET['sort_order']), $allowedSortOrders) ? strtoupper($_GET['sort_order']) : 'ASC';

$daftarLaguTampil = [];
$keyword = '';
$pesan = '';
if (isset($_GET['keyword']) && !empty(trim($_GET['keyword']))) {
    $keyword = trim($_GET['keyword']);
    if (function_exists('cariLaguByJudul')) {
        $daftarLaguTampil = cariLaguByJudul($conn, $keyword, $currentUserId, $sortBy, $sortOrder);
        if (empty($daftarLaguTampil)) {
            $pesan = "Tidak ada lagu ditemukan untuk: &nbsp <strong>" . htmlspecialchars(string: $keyword) . " </strong>";
        } else {
            $pesan = "Menampilkan hasil pencarian untuk: &nbsp <strong>" . htmlspecialchars($keyword) . "</strong>";
        }
    } else {
        $pesan = "Error: Fungsi pencarian tidak ditemukan.";
    }
} else {
    if (function_exists('getAllLagu')) {
        $daftarLaguTampil = getAllLagu($conn, $currentUserId, $sortBy, $sortOrder);
    } else {
        $pesan = "Error: Fungsi daftar lagu tidak ditemukan.";
    }
}
if ($conn) {
    mysqli_close($conn);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPOTIPU - Your Fav Music</title>
    <link rel="stylesheet" href="home.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
        <header class="header"> <!-- HEADER : LOGO, SBAR, LOGOUT -->
            <div class="logo"> <img src="/SPOTIPU/assets/LOGO.png" alt="logo" class="logo-img"></div>
            <div class="header-center">
                <form method="GET" action="home.php" class="search-bar">
                    <input type="text" id="scbar" name="keyword" class="search-bar-input" placeholder="Cari Judul...." value="<?php echo htmlspecialchars($keyword); ?>">
                    <button type="submit" class="search-btn"><i class="fas fa-search"></i></button>
                </form>
            </div>
            <a href="/SPOTIPU/action/user/logout.php" class="logout-button" onclick="return confirm('Yakin ingin logout?');">Logout</a>
        </header>
    </br>
        <div class="sort-add-container"> 
            <form method="GET" action="home.php" class="sort-form">
                 <?php if (!empty($keyword)): ?>
                    <input type="hidden" name="keyword" value="<?php echo htmlspecialchars($keyword); ?>">
                 <?php endif; ?>
                <div class="form-group"> <!-- DROPDOWN SORTIR -->
                    <label for="sort_by">Urutkan Berdasarkan:</label> 
                    <select name="sort_by" id="sort_by">
                        <option value="judul" <?php echo ($sortBy === 'judul' ? 'selected' : ''); ?>>Judul</option>
                        <option value="artist" <?php echo ($sortBy === 'artist' ? 'selected' : ''); ?>>Artis</option>
                        <option value="genre" <?php echo ($sortBy === 'genre' ? 'selected' : ''); ?>>Genre</option>
                        <option value="tahun" <?php echo ($sortBy === 'tahun' ? 'selected' : ''); ?>>Tahun</option>
                    </select>
                </div>
                <div class="form-group"> <!-- TOMBOL SORTIR -->
                    <label for="sort_order">Urutan:</label>
                    <select name="sort_order" id="sort_order">
                        <option value="ASC" <?php echo ($sortOrder === 'ASC' ? 'selected' : ''); ?>>Menaik (A-Z, 0-9)</option>
                        <option value="DESC" <?php echo ($sortOrder === 'DESC' ? 'selected' : ''); ?>>Menurun (Z-A, 9-0)</option>
                    </select>
                </div>
                <div class="sort-buttons">
                    <button type="submit" class="sort-submit-btn">Urutkan</button>
                    <button type="button" id="addSongBtn">Tambah Lagu</button>
                </div>
            </form>
        </div>

        <?php if (!empty($pesan)): ?>
        <div class="search-info"><?php echo $pesan; ?></div>
        <?php endif; ?>

        <div class="song-list"> <!-- DAFTAR LAGU -->
            <?php
            if (!empty($daftarLaguTampil)):
                foreach ($daftarLaguTampil as $lagu):
                    $songId = $lagu['id'] ?? null;
                    $judulLagu = htmlspecialchars($lagu['judul']);
                    $artistLagu = htmlspecialchars($lagu['artist'] ?? 'N/A');
                    $genreLagu = htmlspecialchars($lagu['genre'] ?? '');
                    $tahunLagu = htmlspecialchars($lagu['tahun'] ?? '');
            ?>
                <div class="song-card"
                    data-id="<?php echo $songId; ?>"
                    data-judul="<?php echo $judulLagu; ?>"
                    data-artist="<?php echo $artistLagu; ?>"
                    data-genre="<?php echo $genreLagu; ?>"
                    data-tahun="<?php echo $tahunLagu; ?>">
                    
                    <div class="song-info-main">
                        <div class="song-title"><?php echo $judulLagu ?></div>
                        <div class="song-artist"><?php echo $artistLagu ?></div>
                        <div class="song-genre"><?php echo $genreLagu ?></div>
                    </div>
                    <span class="song-year"><?php echo $tahunLagu ?></span>
                    <?php if ($songId):?>
                    <div class="song-actions">
                        <button class="song-actions-btn" aria-label="Opsi lainnya">
                            <i class="fas fa-ellipsis-v"></i> 
                        </button>
                        <div class="dropdown-menu">
                            <a  class="dropdown-item edit-link">Edit</a>
                            <a href="/SPOTIPU/action/lagu/hapus_lagu.php?id=<?php echo $songId; ?>"
                               class="dropdown-item delete-link"
                               onclick="return confirm('Apakah Anda yakin ingin menghapus lagu ini?');"> Hapus
                            </a>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            <?php
                endforeach;
            elseif (empty($keyword)):
            ?>
                 <div class="no-songs">
                    Belum ada lagu dalam daftar.
                </div>
            <?php endif;?>
        </div>
    </div>

    <div id="addSongModal" class="modal"> <!-- MODAL TAMBAH LAGU -->
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <h2>Tambah Lagu Baru</h2>
            <form action="/SPOTIPU/action/lagu/tambah_lagu.php" method="POST">
                <label for="judul">Judul Lagu:</label>
                <input type="text" id="judul" name="judul" required>

                <label for="artist">Artis:</label>
                <input type="text" id="artist" name="artist" required>

                <label for="genre">Genre:</label>
                <input type="text" id="genre" name="genre">

                <label for="tahun">Tahun Rilis:</label>
                <input type="number" id="tahun" name="tahun" min="1900" max="<?php echo date('Y'); ?>">

                <button type="submit">Simpan Lagu</button>
            </form>
        </div>
    </div>
    
    <div id="editSongModal" class="modal"> <!-- MODAL EDIT LAGU -->
        <div class="modal-content">
            <span class="close-btn">&times;</span> <h2>Edit Lagu</h2>
            <form action="/SPOTIPU/action/lagu/edit_lagu.php" method="POST">
                <input type="hidden" id="edit-song-id" name="id_lagu">

                <label for="edit-judul">Judul Lagu:</label>
                <input type="text" id="edit-judul" name="judul" required>

                <label for="edit-artist">Artis:</label>
                <input type="text" id="edit-artist" name="artist" required>

                <label for="edit-genre">Genre:</label>
                <input type="text" id="edit-genre" name="genre">

                <label for="edit-tahun">Tahun Rilis:</label>
                <input type="number" id="edit-tahun" name="tahun" min="1900" max="<?php echo date('Y'); ?>">

                <button type="submit">Update Lagu</button>
            </form>
        </div>
    </div>
    <script src="script.js"></script>

    <footer class="footer"> <!-- FOOTER -->
         <p>© 2025 SPOTIPU — Buat Kamu Yang Suka Digidaw.</p>
    </footer>
</body>
</html>