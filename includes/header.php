<header>
    <div id="header">
        <div id="titleDiv">
            <h1 id="title">InspoFormal: <?php if ( isset($title) ) {
                echo $title;
                } ?></h1>
        </div>

        <!-- Implement login bar here -->
<?php if (!(is_logged_in())){ ?>


        <!-- If user not loggedin -->
        <div class="loginDiv">
            <form id="loginForm" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'] ); ?>" method="post">
                <label for="username">Username: </label>
                <input id="username" type="text" name="username"/>

                <label for="password">Password: </label>
                <input id="password" type="password" name="password"/>

                <button name="login" type="submit">Log In</button>
        </div>
<?php }

if (is_logged_in()){ ?>
        <!-- If user logged in -->
        <div class="loginDiv">
            <!-- Logout link here -->
            <form id="logoutForm" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'] ); ?>" method="post">
                Welcome, <?php echo htmlspecialchars($online_user['username']); ?>
                <button name="logout" type="submit">Log Out</button>
        </div>
<?php } ?>
    </div>
</header>
