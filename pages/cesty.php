<?php
session_start();
$logged_in = isset($_SESSION['username']);
$username = $logged_in ? $_SESSION['username'] : null;
$is_admin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'];

if (!$logged_in) {
    header("Location: reglog.php");
    exit();
}

require_once '../backend/config.php';
$user_id = $_SESSION['user_id'];

$filter = isset($_GET['filter']) ? $_GET['filter'] : 'vse';
$allowed_filters = ['vse', 'plánovaná', 'probíhající', 'dokončená'];
if (!in_array($filter, $allowed_filters)) $filter = 'vse';

if ($filter === 'vse') {
    $stmt = $conn->prepare("SELECT * FROM trips WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->bind_param("i", $user_id);
} else {
    $stmt = $conn->prepare("SELECT * FROM trips WHERE user_id = ? AND status = ? ORDER BY created_at DESC");
    $stmt->bind_param("is", $user_id, $filter);
}
$stmt->execute();
$trips = $stmt->get_result();
$stmt->close();

// Načtení editované cesty
$edit_trip = null;
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $es = $conn->prepare("SELECT * FROM trips WHERE id = ? AND user_id = ?");
    $es->bind_param("ii", $edit_id, $user_id);
    $es->execute();
    $edit_trip = $es->get_result()->fetch_assoc();
    $es->close();
}
?>
<!DOCTYPE html>
<html lang="cs" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Moje cesty | JOURNEYO</title>
    <link rel="icon" href="../img/favicon.ico">
    <link rel="stylesheet" href="../styles/cesty.css">
    <link rel="stylesheet" href="../styles/themes.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous">
</head>
<body>
    <header>
        <a href="uvod.php"><img src="../img/nvlogo.png" alt="JY" class="logo"></a>
        <div class="menu-btn"></div>
        <div class="nav">
            <div class="nav-items">
                <a href="uvod.php">Úvod</a>
                <a href="destinace.php">Destinace</a>
                <?php if ($logged_in): ?>
                    <a href="oblibene.php">Oblíbené</a>
                    <a href="cesty.php" class="active">Cesty</a>
                    <a href="dashboard.php">Dashboard</a>
                <?php endif; ?>
                <?php if ($is_admin): ?>
                    <a href="../admin/admin.php">Admin</a>
                <?php endif; ?>
                <?php if ($logged_in): ?>
                    <div class="user-info">
                        <span><?php echo htmlspecialchars($username); ?></span>
                        <a href="../backend/logout.php">Odhlásit se</a>
                    </div>
                <?php else: ?>
                    <a href="reglog.php">Přihlášení</a>
                <?php endif; ?>
                <button class="theme-toggle" onclick="toggleTheme()" title="Přepnout motiv">
                    <i class="fa-solid fa-circle-half-stroke"></i>
                </button>
            </div>
        </div>
    </header>

    <div class="cesty-hero">
        <span class="page-label">Cestování</span>
        <h1 class="page-title">Moje cesty</h1>
    </div>

    <main class="cesty-main">
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">
                <?php
                $msgs = ['added' => 'Cesta byla přidána.', 'edited' => 'Cesta byla upravena.', 'deleted' => 'Cesta byla smazána.'];
                echo $msgs[$_GET['success']] ?? '';
                ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-error">Nastala chyba, zkuste to znovu.</div>
        <?php endif; ?>

        <!-- Formulář přidat / upravit -->
        <section class="trip-form-section">
            <h2><?php echo $edit_trip ? 'Upravit cestu' : 'Přidat novou cestu'; ?></h2>
            <form action="../backend/<?php echo $edit_trip ? 'edit_trip' : 'add_trip'; ?>.php" method="POST" class="trip-form">
                <?php if ($edit_trip): ?>
                    <input type="hidden" name="trip_id" value="<?php echo $edit_trip['id']; ?>">
                <?php endif; ?>
                <div class="form-row">
                    <div class="form-group">
                        <label>Název cesty</label>
                        <input type="text" name="name" maxlength="100" required
                            value="<?php echo $edit_trip ? htmlspecialchars($edit_trip['name']) : ''; ?>"
                            placeholder="Např. Letní dovolená 2025">
                    </div>
                    <div class="form-group">
                        <label>Destinace</label>
                        <input type="text" name="destination" maxlength="100" required
                            value="<?php echo $edit_trip ? htmlspecialchars($edit_trip['destination']) : ''; ?>"
                            placeholder="Např. Skotsko">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Datum od</label>
                        <input type="date" name="date_from"
                            value="<?php echo $edit_trip ? $edit_trip['date_from'] : ''; ?>">
                    </div>
                    <div class="form-group">
                        <label>Datum do</label>
                        <input type="date" name="date_to"
                            value="<?php echo $edit_trip ? $edit_trip['date_to'] : ''; ?>">
                    </div>
                    <div class="form-group">
                        <label>Stav</label>
                        <select name="status">
                            <?php foreach (['plánovaná', 'probíhající', 'dokončená'] as $s): ?>
                                <option value="<?php echo $s; ?>" <?php echo ($edit_trip && $edit_trip['status'] === $s) ? 'selected' : ''; ?>>
                                    <?php echo ucfirst($s); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label>Poznámky</label>
                    <textarea name="notes" rows="3" placeholder="Volitelné poznámky..."><?php echo $edit_trip ? htmlspecialchars($edit_trip['notes']) : ''; ?></textarea>
                </div>
                <div class="form-actions">
                    <button type="submit"><?php echo $edit_trip ? 'Uložit změny' : 'Přidat cestu'; ?></button>
                    <?php if ($edit_trip): ?>
                        <a href="cesty.php" class="btn-cancel">Zrušit</a>
                    <?php endif; ?>
                </div>
            </form>
        </section>

        <!-- Filtr -->
        <section class="trips-list-section">
            <div class="filter-bar">
                <a href="cesty.php?filter=vse" class="<?php echo $filter === 'vse' ? 'active' : ''; ?>">Všechny</a>
                <a href="cesty.php?filter=plánovaná" class="<?php echo $filter === 'plánovaná' ? 'active' : ''; ?>">Plánované</a>
                <a href="cesty.php?filter=probíhající" class="<?php echo $filter === 'probíhající' ? 'active' : ''; ?>">Probíhající</a>
                <a href="cesty.php?filter=dokončená" class="<?php echo $filter === 'dokončená' ? 'active' : ''; ?>">Dokončené</a>
            </div>

            <?php if ($trips->num_rows === 0): ?>
                <p class="no-trips">Žádné cesty nenalezeny. Přidejte svou první cestu výše!</p>
            <?php else: ?>
                <div class="trips-grid">
                    <?php while ($trip = $trips->fetch_assoc()): ?>
                        <div class="trip-card status-<?php echo htmlspecialchars($trip['status']); ?>">
                            <div class="trip-card-header">
                                <h3><?php echo htmlspecialchars($trip['name']); ?></h3>
                                <span class="status-badge"><?php echo htmlspecialchars($trip['status']); ?></span>
                            </div>
                            <div class="trip-card-body">
                                <p><i class="fa-solid fa-location-dot"></i> <?php echo htmlspecialchars($trip['destination']); ?></p>
                                <?php if ($trip['date_from'] || $trip['date_to']): ?>
                                    <p><i class="fa-regular fa-calendar"></i>
                                        <?php echo $trip['date_from'] ? date('d.m.Y', strtotime($trip['date_from'])) : '?'; ?>
                                        –
                                        <?php echo $trip['date_to'] ? date('d.m.Y', strtotime($trip['date_to'])) : '?'; ?>
                                    </p>
                                <?php endif; ?>
                                <?php if (!empty($trip['notes'])): ?>
                                    <p class="trip-notes"><?php echo nl2br(htmlspecialchars($trip['notes'])); ?></p>
                                <?php endif; ?>
                            </div>
                            <div class="trip-card-actions">
                                <a href="cesty.php?edit=<?php echo $trip['id']; ?>" class="btn-edit"><i class="fa-solid fa-pen"></i> Upravit</a>
                                <form action="../backend/delete_trip.php" method="POST" onsubmit="return confirm('Opravdu smazat tuto cestu?');">
                                    <input type="hidden" name="trip_id" value="<?php echo $trip['id']; ?>">
                                    <button type="submit" class="btn-delete"><i class="fa-solid fa-trash"></i> Smazat</button>
                                </form>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php endif; ?>
        </section>
    </main>

    <footer>
        <div class="footerContainer">
            <div class="footerSocialMediaIcons">
                <a href="https://www.youtube.com/channel/UC5_X-Wk23b04S3blgQPcBSw"><i class="fa-brands fa-youtube"></i></a>
                <a href="https://www.instagram.com/honza.mak/"><i class="fa-brands fa-instagram"></i></a>
                <a href="https://www.facebook.com/jan.makovicky.52/?locale=cs_CZ"><i class="fa-brands fa-facebook"></i></a>
            </div>
            <div class="footerBottom">
                <p>Copyright &copy;2024-2025; Journeyo</p>
            </div>
            <div class="footerNavbar">
                <ul>
                    <li><a href="uvod.php">Úvod</a></li>
                    <li><a href="destinace.php">Destinace</a></li>
                    <li><a href="oblibene.php">Oblíbené</a></li>
                    <li><a href="cesty.php">Cesty</a></li>
                    <li><a href="dashboard.php">Dashboard</a></li>
                    <?php if ($is_admin): ?>
                        <li><a href="../admin/admin.php">Admin</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </footer>

    <script>
        const menuBtn = document.querySelector(".menu-btn");
        const navigation = document.querySelector(".nav");
        menuBtn.addEventListener("click", () => {
            menuBtn.classList.toggle("active");
            navigation.classList.toggle("active");
        });

        document.addEventListener("DOMContentLoaded", function () {
            var header = document.querySelector("header");
            window.addEventListener("scroll", function () {
                header.classList.toggle("scrolled", window.scrollY > 0);
            });
        });

        // Dark mode
        function toggleTheme() {
            document.body.classList.toggle('light-mode');
            localStorage.setItem('theme', document.body.classList.contains('light-mode') ? 'light' : 'dark');
        }
        if (localStorage.getItem('theme') === 'light') document.body.classList.add('light-mode');
    </script>
</body>
</html>
