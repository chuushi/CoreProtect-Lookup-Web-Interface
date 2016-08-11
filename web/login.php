<?php 
// Login Page
// (c) SimonOrJ, 2015-2016

// Testing script
error_reporting(-1);ini_set('display_errors', 'On');

$c = require "config.php";

require "res/php/login.php";
$login = new Login($c);

if (empty($_REQUEST['action'])) {
    $status = $login->check();
} else {
    if ($_REQUEST['action'] === "login") {
        if (($status = $login->login($_POST['bah'], $_POST['dun'], !empty($_POST['pen']))) === true) {
            header("Location: ".$_GET['landing']);
        } elseif ($status === false) {
            $msg = array("Wrong username or password.", "danger");
        }
    } elseif ($_REQUEST['action'] === "logout") {
        if ($login->logout()) {
            $msg = array("You have been logged out successfully.", "success");
        } else {
            // You'll probably never get this message.
            $msg = array("You were logged out unsuccessfully..?", "warning");
        }
        $status = false;
    }
}

// Message generator
if (empty($msg)) {
        if ($status === false) {
        $msg = array("Please login.", "info");
    } else {
        $msg = array("You are currently logged in as <b>\"".$login->username()."\"</b>.", "success");
        if ($status === null) {
            $msg[0] .= "  This account is currently locked.";
            $msg[1] = "danger";
        }
    }
}

require "res/php/webtemplate.php";
$template = new WebTemplate($c);

?><!DOCTYPE html>
<html>
  
  <?php $template->head(); ?>
  
  <body>
  <!--
    <nav id="top" class="navbar navbar-light bg-faded navbar-full">
      <div class="container">
        <h1 class="navbar-brand">CoreProtect Lookup Web Interface</h1>
        <a href="index.php?action=clear_login" class="btn btn-info-outline pull-xs-right disabled">logout</a>
      </div>
    </nav>
    -->
    <?php $template->navbar($status); ?>
    <div class="container">
      <div class="card">
      <div class="card-header h4">Login</div>
      <form class="card-block" action="login.php<?php if (!empty($_GET['landing'])) echo "?landing=".urlencode($_GET['landing']);?>" method="post">
      	<input type="hidden" name="action" value="login">
        <div class="form-group row">
          <div class="col-sm-12">
            <div class="alert alert-<?php echo $msg[1];?>">
              <?php echo $msg[0];?>
            </div>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-2 form-control-label" for="inputUser">Username</label>
          <div class="col-sm-10"><input class="form-control" type="text" id="inputUser" placeholder="Username" name="bah" required autofocus></div>
        </div>
        <div class="form-group row">
          <label class="col-sm-2 form-control-label" for="inputPass">Password</label>
          <div class="col-sm-10"><input class="form-control" type="password" id="inputPass" placeholder="Password" name="dun" required></div>
        </div>
        <div class="row">
          <div class="col-sm-offset-2 col-sm-10">
            <div class="checkbox">
              <label for="remember">
                <input type="checkbox" id="remember" name="pen"> Remember me
              </label>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-offset-2 col-sm-10">
            <button type="submit" class="btn btn-secondary">Sign in</button>
          </div>
        </div>
      </form>
      </div>
    </div>
  </body>
</html>