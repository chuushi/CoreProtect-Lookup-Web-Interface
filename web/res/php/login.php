<?php 
// Original Login Script by richcheting from http://www.ricocheting.com/code/php/simple-login

// Login
// Constructor: @array Configuration[, @string PostLoginPage]
// (c) SimonOrJ, 2015-2016

// TODO: Make a more secure login sessions.

class Login{
    private $prefix = "CoLogin_",
        $user = "",
        $pass = "",
        $c, $landing;
    
    public function __construct(&$config, $landing = "./") {
        $this->c = &$config;
        $this->cookie_duration = $this->c['login']['duration']*86400;
        $this->landing = $landing;
    }
    
    public function authorize() {
        // If cookie is set (remembered session)
        if (isset($_COOKIE[$this->prefix . 'user'])) {
            // make session equal to cookie
            $_SESSION[$this->prefix . 'user'] = $_COOKIE[$this->prefix . 'user'];
            $_SESSION[$this->prefix . 'pass'] = $_COOKIE[$this->prefix . 'pass'];
        }

        if (isset($_POST['action']) && $_POST['action'] == "login") {
            // Login
            $this->user = $_POST['user'];
            $this->pass = md5($this->prefix . $_POST['pass']);
            $this->check();
            if (isset($_POST['remember'])) {
                setcookie($this->prefix . "user", $this->user, time() + ($this->cookie_duration * 86400));
                setcookie($this->prefix . "pass", $this->pass, time() + ($this->cookie_duration * 86400));
            }

            $_SESSION[$this->prefix . 'user'] = $this->user;
            $_SESSION[$this->prefix . 'pass'] = $this->pass;
        } elseif (isset($_GET['action']) && $_GET['action'] == "prompt") {
            // Not logged in
            session_unset();
            session_destroy();
            if (!empty($_COOKIE[$this->prefix . 'user'])) setcookie($this->prefix . "user", NULL, -1, "/");
            if (!empty($_COOKIE[$this->prefix . 'pass'])) setcookie($this->prefix . "pass", NULL, -1, "/");
            $this->prompt();
        } elseif (isset($_GET['action']) && $_GET['action'] == "clear_session") {
            // Logout
            session_unset();
            session_destroy();
            if (!empty($_COOKIE[$this->prefix . 'user'])) setcookie($this->prefix . "user", NULL, -1, "/");
            if (!empty($_COOKIE[$this->prefix . 'pass'])) setcookie($this->prefix . "pass", NULL, -1, "/");
            $msg = 'Logout successful.';
            $this->prompt($msg);
        } elseif (!isset($_SESSION[$this->prefix . 'pass']) || !isset($_SESSION[$this->prefix . 'user'])) {
            // Ask.
            $this->prompt();
        } else {
            // Check if valid.
            $this->user = $_SESSION[$this->prefix . 'user'];
            $this->pass = $_SESSION[$this->prefix . 'pass'];
            $this->check();
        }
    }

    private function check() {
        // if user does not exist (empty), or if the account is locked, or if the password does not match the user
        if (empty($this->c['user'][$this->user]) || md5($this->prefix . $this->c['user'][$this->user]['pass']) !== $this->pass) {
            // Username and password does not match.
            if (!empty($_COOKIE[$this->prefix . 'user'])) setcookie($this->prefix . "user", NULL, -1, "/");
            if (!empty($_COOKIE[$this->prefix . 'pass'])) setcookie($this->prefix . "pass", NULL, -1, "/");
            session_unset();
            session_destroy();
            $msg = 'Incorrect username or password.';
            $this->prompt($msg);
        } elseif ($this->c['user'][$this->user]['lock'] !== false) {
            // Account is locked.
            $this->prompt($this->c['login']['lockmsg']);
        }
    }

    private function prompt($msg = '') {?><!DOCTYPE html>
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
      <form class="card-block" action="login.php" method="post">
        <div class="form-group row">
          <div class="col-sm-12">
            <span class="text-muted">
              <?=empty($msg)?"Please login.":$msg?>
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
</html><?php
        exit;
    }
}
?>