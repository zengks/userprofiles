<?php
    session_start();

    define('MAX_SIZE', 4000000);
    define('FILE_TYPE', 'image/jpeg');

    $current_username = "";

    $allElements = "";
    
    if(isset($_SESSION['username'])){
        $current_username = $_SESSION['username'];
    }

    if(!isset($_SESSION['logged_in'])){
        header('Location: index.php');
    }

    if(isset($_POST['uniqid']) && $_POST['uniqid'] == $_SESSION['uniqid']){
        // can't submit again
        $allElements = getProfiles($current_username);
    }else{
        // submit!
        storePicture($current_username);
        $allElements = getProfiles($current_username);
        $_SESSION['uniqid'] = $_POST['uniqid'];
    }

    /**
     * @param $pictureName
     * @return $all_id[] - id by selected picture name
     */
    function getPictureId($pictureName)
    {
        $all_id = [];
        $link = mysqli_connect('localhost', 'root', 'root', 'comp3015', '8889');

        if($link)
        {
            $query = "SELECT id, picture FROM profiles";

            if(!(mysqli_query($link, $query)))
            {
                echo mysqli_error($link);
                echo '<br/>';
            }

            $results = mysqli_query($link, $query);
            
            if(count($results) < 1){
                $all_id = [];
            }
            else
            {
                while($item = mysqli_fetch_array($results)){
                    $all_id[$item['picture']] = $item['id'];
                }
            }
        }
        else
        {
            echo "Error: Unable to connect to MySQL.";
            echo '<br/>';
            echo mysqli_connect_error();
            echo '<br/>';
            exit();
        }

        mysqli_close($link);

        return $all_id[$pictureName];
    }

    /**
     * @param $pictureName
     * @return $elements - profile windows with profile picture in HTML codes
     */
    function getProfiles($current_username)
    {
        $elements = "";

        $link = mysqli_connect('localhost', 'root', 'root', 'comp3015', '8889');

        if($link)
        {
            $query = "SELECT id, username, picture FROM profiles";

            if(!(mysqli_query($link, $query)))
            {
                echo mysqli_error($link);
                echo '<br/>';
            }

            $all_profiles = mysqli_query($link, $query);
            
            if(count($all_profiles) < 1){
                $elements = "";
            }
            else
            {
                while($item = mysqli_fetch_array($all_profiles))
                {
                    $picture_path = "profiles/" . $item['picture'];
                    $elements .= createProfile($item['username'], $picture_path, $current_username, $item['picture']);
                }
            }
        }
        else
        {
            echo "Error: Unable to connect to MySQL.";
            echo '<br/>';
            echo mysqli_connect_error();
            echo '<br/>';
            exit();
        }

        mysqli_close($link);

        return $elements;
    }

    /**
     * @param $current_username
     * To store uploaded picture to desired location in the server
     * And call function to add user profile to database
     */
    function storePicture($current_username)
    {
        if($_FILES['picture']['type'] == FILE_TYPE && $_FILES['picture']['size'] <= MAX_SIZE){
        
            $picture_name = md5(time() . $_FILES['picture']['name']) . '.jpg';
            
            $picture_path = "profiles/" . $picture_name;
    
            if(!(move_uploaded_file($_FILES['picture']['tmp_name'], $picture_path)))
            {
                echo 'Failed to upload picture';
                echo '<br/>';
            }

            addProfile($current_username, $picture_name);
        }
    }

    /**
     * @param $current_username
     * @param $picture_name
     * Add user profile to the database
     */
    function addProfile($current_username, $picture_name)
    {
        $link = mysqli_connect('localhost', 'root', 'root', 'comp3015', '8889');

        if($link)
        {
            $query = "INSERT INTO profiles (username, picture) VALUES ('$current_username', '$picture_name')";

            if(!(mysqli_query($link, $query)))
            {
                if(mysqli_error($link) == "Duplicate entry '$current_username' for key 'username'")
                {
                    $query = "UPDATE profiles SET picture='$picture_name' WHERE username='$current_username'";
                    if(!(mysqli_query($link, $query))){
                        echo 'failed to send update query';
                        echo mysqli_error(($link));
                        echo '<br/>';
                    }
                }
                else
                {
                    echo mysqli_error($link);
                    echo '<br/>';
                }
            }
            
        }
        else
        {
            echo "Error: Unable to connect to MySQL.";
            echo '<br/>';
            echo mysqli_connect_error();
            echo '<br/>';
            exit();
        }

        mysqli_close($link);
    }

    /**
     * @param $name
     * @param $picturePath
     * @param $current_username
     * @param $pictureName
     * Create display window for each user profile
     */
    function createProfile($name, $picturePath, $current_username, $pictureName)
    {
        $profile_user_id = getPictureId($pictureName);
        $profile_div = "";

        if($name == $current_username)
        {
            $profile_div = "<div class=\"col-md-4\">
                            <div class=\"panel panel-info\">
                                <div class=\"panel-heading\">
                                    <span>". $name ."</span>
                                    <span class=\"pull-right text-muted\">
                                        <a class=\"\" href=\"delete.php?id=".$profile_user_id. "\">
                                            <i class=\"fa fa-trash\"></i> Delete
                                        </a>
                                    </span>
                                </div>
                                <div class=\"panel-body\">
                                    <p class=\"text-muted\"></p>"
                                . "<img style=\"width: 100%; height: auto;\" src=" . $picturePath . " alt = \"Profile Picture of " . $name . "\"". ">" .
                                "</div>
                                <div class=\"panel-footer\">
                                    <p></p>
                                </div>
                            </div>
                        </div>\n";
        }
        else
        {
            $profile_div = "<div class=\"col-md-4\">
                            <div class=\"panel panel-info\">
                                <div class=\"panel-heading\">
                                    <span>". $name ."</span>
                                </div>
                                <div class=\"panel-body\">
                                    <p class=\"text-muted\"></p>"
                                . "<img style=\"width: 100%; height: auto;\" src=" . $picturePath . " alt = \"Profile Picture of " . $name . "\"". ">" .
                                "</div>
                                <div class=\"panel-footer\">
                                    <p></p>
                                </div>
                            </div>
                        </div>\n";
        }

        return $profile_div;
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
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <hr/>
                <button class="btn btn-default" data-toggle="modal" data-target="#newPost"><i class="fa fa-comment"></i> New Profile</button>
                <a href="logout.php" class="btn btn-default pull-right"><i class="fa fa-sign-out"> </i> Logout</a>
                <hr/>
            </div>
        </div>

        <div class="row">
            <?php
                echo $allElements;
            ?>
        </div>

    </div>
</div>

<div id="newPost" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form role="form" method="post" action="profiles.php" enctype="multipart/form-data">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">New Profile</h4>
                </div>
                <div class="modal-body">
                        <div class="form-group">
                            <label>Username</label>
                            <input class="form-control" disabled value=<?php echo $_SESSION['username']; ?> />
                        </div>
                        <div class="form-group">
                            <label>Profile Picture</label>
                            <input class="form-control" type="file" name="picture">
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <input type="hidden" name="uniqid" value=<?php echo uniqid(); ?> />
                    <input type="submit" class="btn btn-primary" value="Submit!" name="submit"/>
                </div>
            </div><!-- /.modal-content -->
        </form>
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

</body>
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
</html>
