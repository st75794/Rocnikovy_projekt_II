<?php
session_start();
$logged_in = isset($_SESSION['username']);
$username = $logged_in ? $_SESSION['username'] : null;
$is_admin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'];

$is_favorite = false;
$conn = null;
if ($logged_in && isset($_SESSION['user_id'])) {
    require_once '../backend/config.php';
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT 1 FROM favorites WHERE user_id = ? AND destination_id = 4");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->store_result();
    $is_favorite = $stmt->num_rows > 0;
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>JOURNEYO</title>
        <link rel="icon" href="../img/favicon.ico">
        <link rel="stylesheet" href="../styles/zeme.css">
        <link rel="stylesheet" href="../styles/themes.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    </head>
    <body>
        <header>
            <a href="../pages/uvod.php"><img src="../img/nvlogo.png" alt="JY" class="logo"></a>
            <div class="menu-btn"></div>
            <div class="nav">
                <div class="nav-items">
                    <a href="../pages/uvod.php">Úvod</a>
                    <a href="../pages/destinace.php" class="active">Destinace</a>
                    <?php if ($logged_in): ?>
                        <a href="../pages/oblibene.php">Oblíbené</a>
                        <a href="../pages/cesty.php">Cesty</a>
                        <a href="../pages/dashboard.php">Dashboard</a>
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
                        <a href="../pages/reglog.php">Přihlášení</a>
                    <?php endif; ?>
                    <button class="theme-toggle" onclick="toggleTheme()" title="Přepnout motiv"><i class="fa-solid fa-circle-half-stroke"></i></button>
                </div>
            </div>
        </header>

        <div class="dest-hero-image">
            <img src="../img/rakousko.jpg" alt="Rakousko">
            <div class="dest-hero-overlay">
                <a href="../pages/destinace.php" class="hero-back-btn"><i class="fa-solid fa-arrow-left"></i> Zpět</a>
                <h1 class="country-name">Rakousko <?php if ($logged_in): ?><span class="favorite-star <?php echo $is_favorite ? 'filled' : 'empty'; ?>" data-destination-id="4" title="Přidat/Odebrat z oblíbených"><?php echo $is_favorite ? '★' : '☆'; ?></span><?php endif; ?></h1>
            </div>
        </div>

        <main>
            <section class="destination-detail">
                <div class="basic-info">
                    <h2>Základní informace</h2>
                    <ul>
                        <li><strong>Rozloha:</strong> 83 879 km²</li>
                        <li><strong>Počet obyvatel:</strong> 9 milionů</li>
                        <li><strong>Hlavní město:</strong> Vídeň</li>
                        <li><strong>Oficiální jazyk:</strong> Němčina/li>
                        <li><strong>Měna:</strong> Euro (EUR)</li>
                    </ul>
                </div>
    
                <div class="description">
                    <h2>Popis destinace</h2>
                    <p>
                        Rakousko, srdce Evropy, láká svou dechberoucí přírodou,
                         bohatou historií a kulturou. Objevte majestátní Alpy, 
                         idylická jezera jako Hallstatt nebo Wolfgangsee, a 
                         prozkoumejte elegantní Vídeň s jejími zámky a 
                         kavárnami. Užijte si procházky po Salzburku, rodišti 
                         Mozarta, nebo ochutnejte vyhlášený sachr dort. Země 
                         je ideální pro milovníky hor, hudby i kulinářských 
                         zážitků.
                    </p>
                </div>
    
                <div class="highlights">
                    <h2>Doporučená místa</h2>
                    <ul>
                        <li>Vídeň (Štýrský dóm, Schönbrunn)</li>
                        <li>Salcburk (rodné město Mozarta)</li>
                        <li>Hallstatt (malebná alpská vesnice)</li>
                        <li>Innsbruck (alpské město, ideální pro zimní sporty)</li>
                        <li>Grossglockner (nejvyšší hora Rakouska)</li>
                    </ul>
                </div>
    
                <div class="activities">
                    <h2>Co dělat v Rakousku</h2>
                    <ul>
                        <li>Prohlídka vídeňských paláců a muzeí</li>
                        <li>Lyžování a snowboarding v Alpách</li>
                        <li>Turistika kolem Hallstattu a jezera Wolfgangsee</li>
                        <li>Poslech koncertu klasické hudby ve Vídni</li>
                        <li>Objevování tradičních rakouských kaváren a ochutnávka sachr-tortu</li>
                    </ul>
                </div>
            </section>
    
            <a href="../pages/destinace.php" class="back-button">Zpět na seznam destinací</a>
        </main>

        <?php
        require_once '../backend/config.php';
        $destination_id = 4;
        $stmt = $conn->prepare("SELECT r.rating, r.review, r.created_at, u.username FROM reviews r JOIN users u ON r.user_id = u.id WHERE r.destination_id = ? ORDER BY r.created_at DESC");
        $stmt->bind_param("i", $destination_id);
        $stmt->execute();
        $reviews = $stmt->get_result();

        $avg_stmt = $conn->prepare("SELECT AVG(rating) as avg_rating FROM reviews WHERE destination_id = ?");
        $avg_stmt->bind_param("i", $destination_id);
        $avg_stmt->execute();
        $avg_result = $avg_stmt->get_result()->fetch_assoc();
        $avg_rating = round($avg_result['avg_rating'], 1);
        ?>

        <section class="reviews">
            <div class="reviews-header">
                <h2>Recenze</h2>
                <?php if ($avg_rating): ?>
                    <div class="stars"><?php $rounded = round($avg_rating); for ($i = 1; $i <= 5; $i++) { echo $i <= $rounded ? '★' : '☆'; } ?></div>
                    <p>Průměrné hodnocení: <?php echo $avg_rating; ?> / 5</p>
                <?php else: ?>
                    <p>Zatím bez hodnocení</p>
                <?php endif; ?>
            </div>
            <?php while ($r = $reviews->fetch_assoc()): ?>
                <div class="review-item">
                    <strong><?php echo htmlspecialchars($r['username']); ?></strong>
                    <span class="review-stars"><?php for ($i = 1; $i <= 5; $i++) { echo $i <= $r['rating'] ? '★' : '☆'; } ?></span>
                    <p><?php echo nl2br(htmlspecialchars($r['review'])); ?></p>
                    <small><?php echo $r['created_at']; ?></small>
                </div>
            <?php endwhile; ?>

            <?php if ($logged_in): ?>
                <form action="../backend/add_review.php" method="POST" class="review-form">
                    <h3>Přidat recenzi</h3>
                    <label for="rating">Hodnocení (1–5):</label>
                    <select name="rating" id="rating" required>
                        <option value="">-- vyberte --</option>
                        <?php for ($i = 1; $i <= 5; $i++) echo "<option value=\"$i\">$i ★</option>"; ?>
                    </select>
                    <label for="review">Text recenze:</label>
                    <textarea name="review" id="review" rows="4" required placeholder="Sdílejte svůj zážitek..."></textarea>
                    <input type="hidden" name="destination_id" value="<?php echo $destination_id; ?>">
                    <button type="submit">Odeslat recenzi</button>
                </form>
            <?php else: ?>
                <p class="review-login-hint"><a href="../pages/reglog.php">Přihlaste se</a> pro přidání recenze.</p>
            <?php endif; ?>
        </section>

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
                        <li><a href="../pages/uvod.php">Úvod</a></li>
                        <li><a href="../pages/destinace.php">Destinace</a></li>
                        <?php if ($logged_in): ?>
                            <li><a href="../pages/oblibene.php">Oblíbené</a></li>
                        <?php else: ?>
                            <li><a href="../pages/reglog.php">Přihlášení</a></li>
                        <?php endif; ?>
                        <?php if ($is_admin): ?>
                            <li><a href="../admin/admin.php">Admin</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </footer>

        <script>
            function toggleTheme() {
                document.body.classList.toggle('light-mode');
                localStorage.setItem('theme', document.body.classList.contains('light-mode') ? 'light' : 'dark');
            }
            if (localStorage.getItem('theme') === 'light') document.body.classList.add('light-mode');

            function showToast(message, type) {
                const toast = document.createElement('div');
                toast.className = 'toast toast-' + type;
                const icon = type === 'add' ? 'fa-star' : 'fa-star-half-stroke';
                toast.innerHTML = '<i class="fa-solid ' + icon + '"></i> ' + message;
                document.body.appendChild(toast);
                requestAnimationFrame(() => requestAnimationFrame(() => toast.classList.add('show')));
                setTimeout(() => {
                    toast.classList.remove('show');
                    setTimeout(() => toast.remove(), 350);
                }, 3000);
            }

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

                const star = document.querySelector(".favorite-star");
                if (star) {
                    star.addEventListener("click", function () {
                        const destId = this.getAttribute("data-destination-id");
                        const action = this.classList.contains("filled") ? "remove" : "add";
                        fetch(`../backend/${action}_favorite.php`, {
                            method: "POST",
                            headers: { "Content-Type": "application/x-www-form-urlencoded" },
                            body: "destination_id=" + encodeURIComponent(destId)
                        })
                        .then(res => res.text())
                        .then(data => {
                            if (this.classList.contains("filled")) {
                                this.classList.replace("filled", "empty");
                                this.innerHTML = "☆";
                            } else {
                                this.classList.replace("empty", "filled");
                                this.innerHTML = "★";
                            }
                            showToast(data, action);
                        });
                    });
                }
            });
        </script>
    </body>
</html>