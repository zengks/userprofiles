<?php

    define('SALT', 'a_very_random_salt_for_this_app');

    /**
     * Look up the user & password pair from the text file.
     *
     * Passwords are simple md5 hashed.
     *
     * Remember, md5() is just for demonstration purposes.
     * Do not do this in production for passwords.
     *
     * @param $user string The username to look up
     * @param $pass string The password to look up
     * @return bool true if found, false if not
     */
    function findUser($user, $pass)
    {
        $found = false;
        $hash   = md5($pass . SALT);

        $link = mysqli_connect('localhost', 'root', 'root', 'comp3015', '8889');    

        if($link)
        {
            echo  "Success: A proper connection to MySQL was made!" . PHP_EOL;

        }else
        {
            echo "Error: Unable to connect to MySQL." . PHP_EOL;
            echo mysqli_connect_error();
            exit();
        }

        $query = "SELECT username, password FROM users";

        $results = mysqli_query($link, $query);

        while($row = mysqli_fetch_array($results)){
            if($row['username'] == $user && $row['password'] == $hash){
                $found = true;
                break;
            }
        }

        mysqli_close($link);
        
        return $found;
    }

    /**
     * Remember, md5() is just for demonstration purposes.
     * Do not do this in production for passwords.
     *
     * @param $data
     * @return bool returns false if fopen() or fwrite() fails
     */
    function saveUser($data)
    {
        $success = false;

        $link = mysqli_connect('localhost', 'root', 'root', 'comp3015', '8889');

        if($link)
        {
            echo  "Success: A proper connection to MySQL was made!" . PHP_EOL;

        }else
        {
            echo "Error: Unable to connect to MySQL." . PHP_EOL;
            echo mysqli_connect_error();
            exit();
        }

        $username   = trim($data['username']);
        $password   = trim($data['password']);
        $hash       = md5($password . SALT);

        $query = "INSERT INTO users (username, password) VALUES ('$username', '$hash')";
        
        if(mysqli_query($link, $query))
        {
            $success = true;
            echo "Successfully added new user to database";
        }
        else
        {
            //check if username entered already existed in the database...
            if(mysqli_error($link) == "Duplicate entry '$username' for key 'username'")
            {
                //set cookie to indicate username already existed...
                $success = false;
                setcookie('user_existed', true);
                setcookie('error_signup', false);
            }
            else
            {
                //set cookie to indicate signup process failed...
                $success = false;
                setcookie('user_existed', false);
                setcookie('error_signup', true);
                echo mysqli_error($link);
                exit();
            }

        }

        mysqli_close($link);

        return $success;
    }

    /**
     * @param $username
     */
    function checkUsername($username)
    {
        return preg_match('/^([a-z]|[0-9]){8,15}$/i', $username);
    }

    /**
     * @param $data
     * @return bool
     */
    function checkSignUp($data)
    {
        $valid = true;

        // if any of the fields are missing
        if( trim($data['username'])        == '' ||
            trim($data['password'])        == '' ||
            trim($data['verify_password']) == '')
        {
            $valid = false;
        }
        elseif(!checkUsername(trim($data['username'])))
        {
            $valid = false;
        }
        elseif(!preg_match('/((?=.*[a-z])(?=.*[0-9])(?=.*[!?|@])){8}/', trim($data['password'])))
        {
            $valid = false;
        }
        elseif($data['password'] != $data['verify_password'])
        {
            $valid = false;
        }

        return $valid;
    }

    /**
     * @param $name
     */
    function filterUserName($name)
    {
        // if it's not alphanumeric, replace it with an empty string
        return preg_replace("/[^a-z0-9]/i", '', $name);
    }
?>