        <nav>
            <h1>ELTE Stadium</h1>
            <ul class="nav-links">
            <?php if ($auth->is_authenticated()) : ?>
                <a href="index.php?logout=1">Log out (<?= $auth->authenticated_user()["email"] ?>)</a>
            <?php else: ?>
                <a href="register.php"><li><button id="sign-up-button"  class="btn">Sign Up</li></a>
                <a href="login.php"><li><button id="log-in-button"  class="btn">Log In</li></a>
            <?php endif; ?>
            </ul>
        </nav>