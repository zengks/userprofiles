<?php
    $msg = "";

    //flag that signup process failed...
    if (isset($_COOKIE['error_message']))
    {
        echo $_COOKIE['error_message'];
        setcookie('error_message', null, time() - 60*60);
    }

    //flag that username already existed in the database...
    if($_COOKIE['user_existed'] == true)
    {
        //warning message for user during signup...
        $msg = 'Username Existed Already...';
        setcookie('user_existed', null, time()- 60*60);
    }
    else
    {
        $msg = "";
    }
?>

<!DOCTYPE html>
<html>
<head>
    <title>COMP 3015</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>

<div id="wrapper">

    <div class="container">
        <div class="row">
            <div class="col-md-4 col-md-offset-4">
                <h1 class="login-panel text-center text-muted">
                    COMP 3015 Assignment 2
                </h1>
                <hr/>
                <div class="login-panel panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Create Account</h3>
                    </div>
                    <div class="panel-body">
                        <form name="signup" role="form" action="redirect.php?from=signup" method="post">
                            <fieldset>
                                <div class="form-group">
                                    <p>
                                        <?php
                                            echo $msg;
                                        ?>
                                    </p>
                                    <input class="form-control"
                                           value=""
                                           name="username"
                                           placeholder="Username"
                                           type="text"
                                           autofocus
                                    />
                                </div>
                                <div class="form-group">
                                    <input class="form-control"
                                           name="password"
                                           placeholder="Password"
                                           type="password"
                                    />
                                </div>
                                <div class="form-group">
                                    <input class="form-control"
                                           name="verify_password"
                                           placeholder="Verify Password"
                                           type="password"
                                    />
                                </div>
                                <input type="submit" class="btn btn-lg btn-info btn-block" value="Sign Up!"/>
                            </fieldset>
                        </form>
                    </div>
                </div>
                <a class="btn btn-sm btn-default" href="login.php">Login</a>
            </div>
        </div>

    </div>
</div>

<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>