<?php 
// Original Login Script by richcheting from http://www.ricocheting.com/code/php/simple-login

// Login
// (c) SimonOrJ, 2015-2016

// __construct ( @array &Configuration )
//   returns nothing.
// check ( @void )
//   returns true on success, false on failure, or null on locked account.
// username ( @void )
//   returns string of the username.
// login ( @string username, @string password[, @boolean remember = false] )
//   same return as check().
// logout ( @void )
//   returns true at all times.

// TODO: Make a more secure login sessions.


class Login {
    private $prefix = "CoLogin_",
        $user = "",
        $pass = "",
        $c;
    
    public function __construct(&$config) {
        $this->c = &$config;
        $this->cookie_duration = $this->c['login']['duration']*86400;
        
        // Start a session.
        session_start();
        
        // If cookies exist
        if (isset($_COOKIE[$this->prefix . 'user'])) {
            // make session equal to cookie
            $_SESSION[$this->prefix . 'user'] = $_COOKIE[$this->prefix . 'user'];
            $_SESSION[$this->prefix . 'pass'] = $_COOKIE[$this->prefix . 'pass'];
        }
        
        // set username and password
        if (!empty($_SESSION[$this->prefix . 'user'])) $this->user = $_SESSION[$this->prefix . 'user'];
        if (!empty($_SESSION[$this->prefix . 'pass'])) $this->pass = $_SESSION[$this->prefix . 'pass'];
    }
    
    // Gets the logged in username.
    public function username() {
        return $this->user;
    }

    // Checks cookie login status.
    public function check() {
        // If user does not exist (empty) or the password does not match the user
        if (empty($this->user) || empty($this->pass)) {
            return false;
        }
        
        if (empty($this->c['user'][$this->user]) || md5($this->prefix . $this->c['user'][$this->user]['pass']) !== $this->pass) {
            $this->logout();
            return false;
        }
        
        // If account is locked
        if ($this->c['user'][$this->user]['lock'] !== false) {
            return null;
        }
        return true;
    }
    
    // Checks login details
    public function login($user, $pass, $remember = false) {
        $this->user = $user;
        $this->pass = md5($this->prefix . $pass);
        
        // If login is unsuccessful
        if (($ret = $this->check()) === false)
            return false;
        
        // If remember is set
        if ($remember) {
            setcookie($this->prefix . "user", $this->user, time() + ($this->cookie_duration * 86400));
            setcookie($this->prefix . "pass", $this->pass, time() + ($this->cookie_duration * 86400));
        }

        $_SESSION[$this->prefix . 'user'] = $this->user;
        $_SESSION[$this->prefix . 'pass'] = $this->pass;
        return $ret;
    }
    
    // Logs the user out
    public function logout() {
        if (!empty($_COOKIE[$this->prefix . 'user'])) setcookie($this->prefix . "user", NULL, -1, "/");
        if (!empty($_COOKIE[$this->prefix . 'pass'])) setcookie($this->prefix . "pass", NULL, -1, "/");
        session_destroy();
        $this->user = null;
        $this->pass = null;
        return true;
    }
}
?>