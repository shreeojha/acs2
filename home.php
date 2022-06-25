<?php
session_start();
include('connect/connection.php');
if (!isset($_SESSION['email'])) {
    # code...
    header('Location: index.php');
    die();
}

?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <title></title>
    <style>
        .button {
            position: fixed;
            top: 10px;
            right: 10px;
            padding: 15px 25px;
            font-size: 24px;
            text-align: center;
            cursor: pointer;
            outline: none;
            color: #fff;
            background-color: #04AA6D;
            border: none;
            border-radius: 15px;
            box-shadow: 0 9px #999;
        }

        .button:hover {
            background-color: #3e8e41
        }

        .button:active {
            background-color: #3e8e41;
            box-shadow: 0 5px #666;
            transform: translateY(4px);
        }
    </style>
</head>
<body>
<div class="button">
    <a href="logout.php">
        <span><strong>LOGOUT</strong></span>
    </a>
</div>

</body>
</html>
