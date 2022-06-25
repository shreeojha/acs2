<?php session_start(); ?>
<?php
include('connect/connection.php');
$err = "";
if (isset($_POST["submit"])) {
  //validation for sql injection
  $uname = mysqli_real_escape_string($connect, $_POST['uname']);
  $email = mysqli_real_escape_string($connect, $_POST['email']);
  $password = mysqli_real_escape_string($connect, $_POST['password']);
  $cpassword = mysqli_real_escape_string($connect, $_POST['cpassword']);

    //validate paswword
    $uppercase = preg_match('@[A-Z]@', $password);
    $lowercase = preg_match('@[a-z]@', $password);
    $number = preg_match('@[0-9]@', $password);
    $specialChars = preg_match('@[^\w]@', $password);

    //date
    date_default_timezone_set("Asia/Kathmandu");
    $date_created = date('M d, Y \a\t h:ia', time());
    //reCaptcha
    $secretKey = "6Lf1cjYgAAAAAOeztQC0F-z7w2EJFh-1eOyOcFri";
      $responseKey = $_POST['g-recaptcha-response'];
      $userIP = $_SERVER['REMOTE_ADDR'];
    $url = "https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$responseKey&remoteip=$userIP";

    $response = file_get_contents($url);
    $response = json_decode($response);
    //var_dump($response);

    $check_query = mysqli_query($connect, "SELECT * FROM login where email ='$email'");
    $rowCount = mysqli_num_rows($check_query);
if ($email == "" || $password == "" || $cpassword == ""){
  $err = "<div class='alert alert-danger alert-dismissible'>
   <a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Please fill the information</div>";

}else {
  if (!empty($email) && !empty($password)) {
      if ($rowCount > 0) {
          $err = "<div class='alert alert-danger alert-dismissible'>
           <a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>The entered Email is already exist!</div>";

      } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
          # code...
          $err = "<div class='alert alert-danger alert-dismissible'>
              <a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>You entered an Invalid Email Format!</div>";
      } elseif ($password != $cpassword) {
          # code...
          $err = "<div class='alert alert-danger alert-dismissible'>
            <a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Your password does not match!</div>";
      } elseif (!$uppercase || !$lowercase || !$number || !$specialChars || strlen($password) < 8) {
          # code...
          $err = "<div class='alert alert-danger alert-dismissible'>
            <a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Password must be 8 characters in length with atleast one uppercase and lowercase letter, one numeric and special character <strong>King4life+</strong></div>";

      }
//validation for reCaptchacheck
      elseif (!$response->success) {
          # code...
          $err = "<div class='alert alert-danger alert-dismissible'>
                <a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Check the box, to ensure your not a robot.</div>";

      } else {
        $check_query = mysqli_query($connect, "SELECT * FROM login where username ='$uname'");
        $rowCount = mysqli_num_rows($check_query);
            if ($rowCount > 0) {
              $err = "<div class='alert alert-danger alert-dismissible'>
               <a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>The entered Username is already exist!</div>";

            }
            else {
              $password_hash = password_hash($password, PASSWORD_BCRYPT);

              $result = mysqli_query($connect, "INSERT INTO login (username, email, password, status,date) VALUES ('$uname','$email', '$password_hash', 0,NOW())");

              if ($result) {
                //otp to verify email or username
                  $otp = rand(100000, 999999);
                  $_SESSION['otp'] = $otp;
                  $_SESSION['mail'] = $email;
                  require "Mail/phpmailer/PHPMailerAutoload.php";
                  $mail = new PHPMailer;

                  $mail->isSMTP();
                  $mail->Host = 'smtp.gmail.com';
                  $mail->Port = 587;
                  $mail->SMTPAuth = true;
                  $mail->SMTPSecure = 'tls';

                  $mail->Username = 'shrisanth@ismt.edu.np';
                  $mail->Password = 'F@ceb00k9';

                  $mail->setFrom('shrisanth@ismt.edu.np', 'OTP Verification');
                  $mail->addAddress($_POST["email"]);

                  $mail->isHTML(true);
                  $mail->Subject = "Your verify code";
                  $mail->Body = "<p>Dear user, </p> <h3>Your verify OTP code is $otp <br></h3>
                      <br><br>
                      <p>With regrads,</p>
                      <b>shrisanth ojha</b>";

                  if (!$mail->send()) {
                      ?>
                      <script>
                          alert("<?php echo "Register Failed, Invalid Email "?>");
                      </script>
                      <?php
                  } else {
                      ?>
                      <script>
                          alert("<?php echo "Register Successfully, OTP sent to " . $email ?>");
                          window.location.replace('verification.php');
                      </script>
                      <?php
                  }
              }
            }
      }
  }
}

}

?>


<!DOCTYPE html>
<html lang="en" >
<head>
  <meta charset="UTF-8">
  <title>Register Here</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css">
  <link rel="stylesheet" href="./aa.css">

</head>
<body>
<!-- partial:index.partial.html -->
<head>
<!--     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css"> -->
    <script src="https://kit.fontawesome.com/1c2c2462bf.js" crossorigin="anonymous"></script>
</head>

<body>

    <div class="container">

        <form class="form-horizontal" id="validateForm" method="post">
            <h1>Register New Account</h1>
            <fieldset>
              <div class="form-group">
                  <label class="col-md-12 control-label" for="textinput">
                      <?php echo $err; ?>
                  </label>

              </div>
              <div class="form-group">
                              <label class="col-md-12 control-label" for="textinput">
                                  Username
                              </label>
                              <div class="col-md-12">
                                  <input type="text" name="uname" value="<?php if(isset($_POST['uname'])){echo htmlentities ($_POST['uname']);}?>"
                                   autocomplete="off"
                                  placeholder="Enter your Username"
                                  class="form-control input-md">
                              </div>
                          </div>
                <!-- Email input-->
                <div class="form-group">
                    <label class="col-md-12 control-label" for="textinput">
                        Email
                    </label>
                    <div class="col-md-12">
                        <input id="email" name="email" value="<?php if(isset($_POST['email'])){echo htmlentities ($_POST['email']);}?>"
                        type="text" autocomplete="off"
                        placeholder="Enter your email address"
                        class="form-control input-md">
                    </div>
                </div>


                <!-- Password input-->
                <div class="form-group">
                    <label class="col-md-12 control-label" for="passwordinput">
                        Password
                    </label>
                    <div class="col-md-12">
                      <input id="password" class="form-control input-md"name="password" type="password" placeholder="Enter your password" >
                        <div class="form-group">
                            <label class="col-md-12 control-label" for="passwordinput">
                                Password
                            </label>
                            <div class="col-md-12">
                              <input id="cpassword" type="password" name="cpassword" class="form-control mb-4" placeholder="Confirm Password...">
                              </div>
<br><br>
                        <span class="show-pass" onclick="toggle()">
                            <i class="far fa-eye" onclick="myFunction(this)"></i>
                        </span><br><br>

                        <div id="popover-password">
                            <p><span id="result"></span></p>
                            <div class="progress">
                                <div id="password-strength"
                                    class="progress-bar"
                                    role="progressbar"
                                    aria-valuenow="40"
                                    aria-valuemin="0"
                                    aria-valuemax="100"
                                    style="width:0%">
                                </div>
                            </div>
                            <ul class="list-unstyled">
                                <li class="">
                                    <span class="low-upper-case">
                                        <i class="fas fa-circle" aria-hidden="true"></i>
                                        &nbsp;Lowercase &amp; Uppercase
                                    </span>
                                </li>
                                <li class="">
                                    <span class="one-number">
                                        <i class="fas fa-circle" aria-hidden="true"></i>
                                        &nbsp;Number (0-9)
                                    </span>
                                </li>
                                <li class="">
                                    <span class="one-special-char">
                                        <i class="fas fa-circle" aria-hidden="true"></i>
                                        &nbsp;Special Character (!@#$%^&*)
                                    </span>
                                </li>
                                <li class="">
                                    <span class="eight-character">
                                        <i class="fas fa-circle" aria-hidden="true"></i>
                                        &nbsp;Atleast 8 Character
                                    </span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
              </div>
                <!-- agreement -->
    <div class="custom-control custom-checkbox" style="padding-left: 15px;">
<!-- <input type="checkbox" class="custom-control-input" name="checkbox">
<label class="custom-control-label"><a href="#" class="link">
<strong>Agreement Policy</strong></a>
</label> -->
<!-- <p class="support-p" style="float: right; padding-right: 15px;">Already have an account?
    <a href="login.php" class="link"><strong>Login</strong></a> -->

</p>
    </div>
                  <div class="g-recaptcha" data-sitekey="6Lf1cjYgAAAAAD0w9L0rsU8Y5Num4ly3lG-zZ7Mw"></div>
                <!-- Button -->
<br>
  <button class="btn admin-reg-btn btn-block" name="submit" type="submit"><strong>SIGN UP</strong></button>
                <div class="form-group">
                    <!-- <a href="#" class="btn login-btn btn-block">
                        Create Account
                    </a> -->

                </div>
                <div class="ex-account text-center">
                    <p>Already have an account? Signin
                        <a href="index.php">here</a>
                    </p>
                    <div class="divider"></div>
                </div>
            </fieldset>
        </form>
    </div>
<!--     <script src="main.js"></script> -->
</body>
<!-- partial -->
  <script  src="./script.js"></script>
<script src="https://www.google.com/recaptcha/api.js" async defer></script>

</body>
</html>
