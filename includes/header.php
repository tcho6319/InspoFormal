<header>
    <div id="header">
        <div id="titleDiv">
            <h1 id="title">InspoFormal: <?php if ( isset($title) ) {
                echo $title;
                } ?></h1>
        </div>

        <!-- Implement login bar here -->
        <!-- If userloggedin -->
        <div class="loginDiv">
            <form id="loginForm" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'] ); ?>" method="post">
                <label for="username">Username: </label>
                <input id="username" type="text" name="username"/>

                <label for="password">Password: </label>
                <input id="password" type="password" name="password"/>

                <button name="login" type="submit">Log In</button>
        </div>

        <!-- If user not logged in -->
        <div class="loginDiv">
            <!-- Logout link here -->
        </div>
    </div>
</header>
