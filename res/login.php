<?php /* CoreProtect LWI - v0.7.1-beta * Login Script by richcheting from http://www.ricocheting.com/code/php/simple-login */require_once __DIR__."/../settings.php";define('LOGIN_USER',$loginUsername);define('LOGIN_PASS',$loginPassword);class Login{var $prefix="login_";var $cookie_duration=21;var $user="";var $pass="";function authorize(){if(isset($_COOKIE[$this->prefix.'user'])){$_SESSION[$this->prefix.'user']=$_COOKIE[$this->prefix.'user'];$_SESSION[$this->prefix.'pass']=$_COOKIE[$this->prefix.'pass'];}if(isset($_POST['action'])&&$_POST['action']=="set_login"){$this->user=$_POST['user'];$this->pass=md5($this->prefix.$_POST['pass']);$this->check();if(isset($_POST['remember'])){setcookie($this->prefix."user",$this->user,time()+($this->cookie_duration*86400));setcookie($this->prefix."pass",$this->pass,time()+($this->cookie_duration*86400));}$_SESSION[$this->prefix.'user']=$this->user;$_SESSION[$this->prefix.'pass']=$this->pass;}elseif(isset($_GET['action'])&&$_GET['action']=="prompt"){session_unset();session_destroy();if(!empty($_COOKIE[$this->prefix.'user']))setcookie($this->prefix."user","blanked",time()-(3600*25));if(!empty($_COOKIE[$this->prefix.'pass']))setcookie($this->prefix."pass","blanked",time()-(3600*25));$this->prompt();}elseif(isset($_GET['action'])&&$_GET['action']=="clear_login"){session_unset();session_destroy();if(!empty($_COOKIE[$this->prefix.'user']))setcookie($this->prefix."user","blanked",time()-(3600*25));if(!empty($_COOKIE[$this->prefix.'pass']))setcookie($this->prefix."pass","blanked",time()-(3600*25));$msg='Logout successful.';$this->prompt($msg);}elseif(!isset($_SESSION[$this->prefix.'pass'])||!isset($_SESSION[$this->prefix.'user'])){$this->prompt();}else{$this->user=$_SESSION[$this->prefix.'user'];$this->pass=$_SESSION[$this->prefix.'pass'];$this->check();}}function check(){if(md5($this->prefix.LOGIN_PASS)!=$this->pass||LOGIN_USER!=$this->user){if(!empty($_COOKIE[$this->prefix.'user']))setcookie($this->prefix."user","blanked",time()-(3600*25));if(!empty($_COOKIE[$this->prefix.'pass']))setcookie($this->prefix."pass","blanked",time()-(3600*25));session_unset();session_destroy();$msg='Incorrect username or password.';$this->prompt($msg);}}function prompt($msg=''){?><!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>CorePortect Lookup Web Interface &bull; by SimonOrJ</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.2/css/bootstrap.min.css" integrity="sha384-y3tfxAZXuh4HwSYylfB+J125MxIs6mR5FOHamPBG064zB+AFeWH94NdvaCBm8qnd" crossorigin="anonymous">
    <link rel="stylesheet" href="res/main.css">
  </head>

  <body>
    <nav id="top" class="navbar navbar-light bg-faded navbar-full">
      <div class="container">
        <h1 class="navbar-brand">CoreProtect Lookup Web Interface</h1>
        <a href="index.php?action=clear_login" class="btn btn-info-outline pull-xs-right disabled">logout</a>
      </div>
    </nav>
    <div class="container">
      <div class="card">
      <div class="card-header h4">Login Page</div>
      <form class="card-block" action="<?php echo $_SERVER['SCRIPT_NAME'];?>" method="post">
        <div class="form-group row">
          <div class="col-sm-12">
            <span class="text-muted">
              <?=empty($msg)?"Enter username and password to continue.":$msg?>
            </span>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-2 form-control-label" for="inputUser">Username</label>
          <div class="col-sm-10"><input class="form-control" type="text" id="inputUser" placeholder="Username" name="user" required autofocus></div>
        </div>
        <div class="form-group row">
          <label class="col-sm-2 form-control-label" for="inputPass">Password</label>
          <div class="col-sm-10"><input class="form-control" type="password" id="inputPass" placeholder="Password" name="pass" required></div>
        </div>
        <div class="row">
          <div class="col-sm-offset-2 col-sm-10">
            <div class="checkbox">
              <label for="remember">
                <input type="checkbox" id="remember" name="remember"> Remember me
              </label>
            </div>
          </div>
        </div>
      	<input type="hidden" name="action" value="set_login">
        <div class="row">
          <div class="col-sm-offset-2 col-sm-10">
            <button type="submit" class="btn btn-secondary">Sign in</button>
          </div>
        </div>
      </form>
      </div>
    </div>
  </body>
</html><?php exit;}}?>