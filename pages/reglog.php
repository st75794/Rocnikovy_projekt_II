<?php
session_start();
$logged_in = isset($_SESSION['username']);
$username = $logged_in ? $_SESSION['username'] : null;
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Journeyo - Přihlášení / Registrace</title>
    <link rel="icon" href="../img/favicon.ico">
    <link rel="stylesheet" href="../styles/reglog.css">
    <link rel="stylesheet" href="../styles/themes.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
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
                        <a href="cesty.php">Cesty</a>
                        <a href="dashboard.php">Dashboard</a>
                    <?php endif; ?>

                    <?php if ($logged_in): ?>
                        <div class="user-info">
                            <span><?php echo htmlspecialchars($username); ?></span>
                            <a href="../backend/logout.php">Odhlásit se</a>
                        </div>
                    <?php else: ?>
                        <a href="reglog.php" class="active">Přihlášení</a>
                    <?php endif; ?>
                    <button class="theme-toggle" onclick="toggleTheme()" title="Přepnout motiv"><i class="fa-solid fa-circle-half-stroke"></i></button>
                </div>
        </div>
    </header>

    <main class="auth-main">
        <div class="auth-container">
            <div id="login-box">
            <h2>Přihlášení</h2>
            <?php
                if (isset($_GET['error'])) {
                    if ($_GET['error'] === 'wrongpass') {
                        echo '<p class="form-error">Nesprávné heslo.</p>';
                    } elseif ($_GET['error'] === 'noemail') {
                        echo '<p class="form-error">Uživatel s tímto emailem neexistuje.</p>';
                    }
                }
                ?>
                <form action="../backend/login.php" method="POST">
                    <input type="email" name="email" placeholder="Email" required>
                    <input type="password" name="password" placeholder="Heslo" required>
                    <button type="submit">Přihlásit se</button>
                </form>
                <p class="toggle-link" onclick="switchToRegister()">Nemáte účet? Registrujte se</p>
            </div>

            <div id="register-box" class="hidden">
                <h2>Registrace</h2>
                <form action="../backend/register.php" method="POST">
                    <input type="text" name="username" placeholder="Uživatelské jméno" minlength="3" maxlength="16" required>
                    <input type="email" name="email" placeholder="Email" minlength="5" maxlength="50" required>
                    <input type="password" name="password" placeholder="Heslo" minlength="10" maxlength="25" required>
                    <button type="submit">Registrovat</button>
                </form>
                <p class="toggle-link" onclick="switchToLogin()">Máte účet? Přihlaste se</p>
            </div>
        </div>
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
                    <?php if ($logged_in): ?>
                        <li><a href="oblibene.php">Oblíbené</a></li>
                    <?php else: ?>
                        <li><a href="reglog.php">Přihlášení</a></li>
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

        function switchToRegister() {
            document.getElementById("login-box").classList.add("hidden");
            document.getElementById("register-box").classList.remove("hidden");
        }

        function switchToLogin() {
            document.getElementById("register-box").classList.add("hidden");
            document.getElementById("login-box").classList.remove("hidden");
        }

        const menuBtn = document.querySelector(".menu-btn");
        const navigation = document.querySelector(".nav");

        menuBtn.addEventListener("click", () => {
            menuBtn.classList.toggle("active");
            navigation.classList.toggle("active");
        });
    </script>
</body>
</html>