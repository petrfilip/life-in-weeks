<?php


class Security
{
    static function isAccessApproved()
    {
        if ($_SESSION["isLogged"] === true) {
            return;
        }

        if ($_POST["password"] == PASSWORD) {
            $_SESSION["isLogged"] = true;
            header('Location: '.$_SERVER['PHP_SELF']);
            die;
        }

        header("Location: login.php");
        die;
    }

    static function logout()
    {
        $_SESSION = [];
        session_destroy();
    }

}