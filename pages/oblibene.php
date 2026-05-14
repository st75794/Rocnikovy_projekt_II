<?php
session_start();
$logged_in = isset($_SESSION['username']);
$username = $logged_in ? $_SESSION['username'] : null;
$is_admin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'];
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>JOURNEYO</title>
        <link rel="icon" href="../img/favicon.ico">
        <link rel="stylesheet" href="../styles/oblibene.css">
        <link rel="stylesheet" href="../styles/themes.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" />
        <style>
            .theme-toggle { background:none; border:1px solid rgba(255,255,255,0.3); color:#fff; border-radius:50%; width:34px; height:34px; cursor:pointer; display:flex; align-items:center; justify-content:center; font-size:1em; transition:0.3s; margin-left:10px; }
            .theme-toggle:hover { border-color:#008af5; color:#008af5; }
        </style>
    </head>
    <body>
        <header>
            <a href="uvod.php"><img src="../img/nvlogo.png" alt="MD" class="logo"></a>
            <div class="menu-btn"></div>
            <div class="nav">
                <div class="nav-items">
                    <a href="uvod.php">Úvod</a>
                    <a href="destinace.php">Destinace</a>
                    <?php if ($logged_in): ?>
                        <a href="oblibene.php" class="active">Oblíbené</a>
                        <a href="cesty.php">Cesty</a>
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
                    <button class="theme-toggle" onclick="toggleTheme()" title="Přepnout motiv"><i class="fa-solid fa-circle-half-stroke"></i></button>
                </div>
            </div>
        </header>
        <section class="pickers">
            <div class="pick">
                <?php
                require_once '../backend/config.php';

                if (!$logged_in) {
                    echo "<p style='color: white;'>Pro zobrazení oblíbených destinací se musíte přihlásit.</p>";
                } else {
                    $user_id = $_SESSION['user_id'];
                    $sql = "SELECT d.* FROM favorites f 
                            JOIN destinations d ON f.destination_id = d.id 
                            WHERE f.user_id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $user_id);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    while ($row = $result->fetch_assoc()) {
                        $img = htmlspecialchars($row['image_url']);
                        $name = htmlspecialchars($row['name']);
                        $page = strtolower($name) . ".php";
                        echo "
                            <a href='../zeme/$page' 
                            class='picker' 
                            style='--img:url(../$img);' 
                            data-text='$name'>
                            </a>";
                    }

                    $stmt->close();
                }
                ?>
            </div>
        </section>

        <?php
        // Doporučené destinace pod obíbenými
        if ($logged_in) {
            require_once '../backend/config.php';

            $sql_recommend = "
                SELECT DISTINCT d.* FROM destinations d
                WHERE d.id NOT IN (
                    SELECT destination_id FROM favorites WHERE user_id = ?
                )
                AND d.category IN (
                    SELECT d2.category FROM destinations d2
                    JOIN favorites f2 ON d2.id = f2.destination_id
                    WHERE f2.user_id = ?
                )
                LIMIT 3
            ";

            $stmt_rec = $conn->prepare($sql_recommend);
            $stmt_rec->bind_param("ii", $user_id, $user_id);
            $stmt_rec->execute();
            $result_rec = $stmt_rec->get_result();

            if ($result_rec->num_rows > 0) {
                echo "<section class='recommendations'>";
                echo "<h2>Mohlo by se vám dále líbit:</h2>";
                echo "<div class='recommend-grid'>";
                while ($row = $result_rec->fetch_assoc()) {
                    $img = htmlspecialchars($row['image_url']);
                    $name = htmlspecialchars($row['name']);
                    $page = strtolower($name) . ".php";
                    echo "
                        <a href='../zeme/$page' 
                        class='recommend-card' 
                        style='--img:url(../$img);' 
                        data-text='$name'>
                        </a>";
                }
                echo "</div></section>";
            }
            $stmt_rec->close();
        }
        ?>

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
                        <?php if ($logged_in): ?>
                            <li><a href="oblibene.php">Oblíbené</a></li>
                        <?php else: ?>
                            <li><a href="reglog.php">Přihlášení</a></li>
                        <?php endif; ?>
                        <?php if ($is_admin): ?>
                            <li><a href="../admin/admin.php">Admin</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </footer>

        <script type="text/javascript">
        function toggleTheme() {
            document.body.classList.toggle('light-mode');
            localStorage.setItem('theme', document.body.classList.contains('light-mode') ? 'light' : 'dark');
        }
        if (localStorage.getItem('theme') === 'light') document.body.classList.add('light-mode');

        const menuBtn = document.querySelector(".menu-btn");
        const navigation = document.querySelector(".nav");

        menuBtn.addEventListener("click", () => {
            menuBtn.classList.toggle("active");
            navigation.classList.toggle("active");
        });

        document.addEventListener("DOMContentLoaded", function() {
            var header = document.querySelector("header");

            window.addEventListener("scroll", function() {
                if (window.scrollY > 0) {
                    header.classList.add("scrolled");
                } else {
                    header.classList.remove("scrolled");
                }
            });
        });
        </script>
    </body>
</html>