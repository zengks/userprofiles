<?php
    require 'includes/functions.php';

    session_start();

    if(count($_POST) > 0)
    {
        if($_GET['from'] == 'login')
        {
            $found = false; // assume not found

            $user = trim($_POST['username']);
            $pass = trim($_POST['password']);

            if(checkUsername($user))
            {
                $found = findUser($user, $pass);

                if($found)
                {
                    $_SESSION['logged_in'] = true;
                    $_SESSION['username'] = $user;

                    echo 'logged in...';
                    header('Location: thankyou.php?from=login&username='.filterUserName($user));
                    exit();
                }
            }else
            {
                echo 'failed to login...';
                setcookie('error_message', 'Login Validation Failed...');
                header('Location: login.php');
                exit();
            }
            header('Location: login.php');
            exit();
        }
        elseif($_GET['from'] == 'signup')
        {
            if(checkSignUp($_POST) && saveUser($_POST))
            {
                $_SESSION['logged_in'] = true;
                $_SESSION['username'] = trim($_POST['username']);

                header('Location: thankyou.php?from=signup&username='.filterUserName(trim($_POST['username'])));
                exit();
            }
            else
            {
                if($_COOKIE['error_signup'] == true){
                    setcookie('error_signup', null, time() - 60*60);
                    setcookie('error_message', 'Signup Validation Failed...');
                    header('Location: signup.php');
                    exit();
                }

                if($_COOKIE['user_existed'] == true){
                    header('Location: signup.php');
                    exit();
                }

                header('Location: signup.php');
                exit();
            }
        }
    }

    header('Location: index.php');
    exit();
?>