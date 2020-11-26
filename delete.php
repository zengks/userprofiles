<?php
    session_start();

    if(!isset($_SESSION['logged_in'])){
        header('Location: index.php');
    }

    $current_username = "";
    $selected_id;

    //validate if retrieved id is valid
    if(isset($_GET['id'])){
        $selected_id = $_GET['id'];
        if(intval($selected_id) <= 0){
            echo 'selected id is: ' . $selected_id;
            echo 'Invalid User Id...Exiting...';
            exit();
        }
    }

    //call delete function to delete user profile selected by id
    if(isset($_SESSION['username'])){
        $current_username = $_SESSION['username'];
        deleteProfile($selected_id, $current_username);
    }

    /**
     * @param $user_id
     * @param $name
     */
    function deleteProfile($user_id, $name)
    {
        $delete_query="";

        $link = mysqli_connect('localhost', 'root', 'root', 'comp3015', '8889');

        if($link)
        {
            $query = "SELECT id, username FROM profiles";
            
            if(!(mysqli_query($link, $query)))
            {
                echo mysqli_error($link);
                echo '<br/>';
            }

            $all_profiles = mysqli_query($link, $query);
            
            while($item = mysqli_fetch_array($all_profiles)){
                if($item['username'] == $name && $item['id'] == $user_id){
                    $delete_query = "DELETE FROM profiles WHERE username = '$name' AND id = '$user_id'";
                }
            }

            if((mysqli_query($link, $delete_query)))
            {
                echo "Successfully Deleted...";
                echo '<br/>';

                //deleted column id and add it back to reset auto_increment counting from 1 again
                $reset_id_query = "ALTER TABLE profiles DROP COLUMN id;";
                $add_id_query = "ALTER TABLE profiles ADD COLUMN id bigint primary key auto_increment FIRST;";
                mysqli_query($link, $reset_id_query);
                mysqli_query($link, $add_id_query);

                header('Location: profiles.php');
                exit();
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
?>