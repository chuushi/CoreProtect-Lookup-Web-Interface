<?php 
// Original Login Script by richcheting from http://www.ricocheting.com/code/php/simple-login

// Login
// (c) SimonOrJ, 2015-2016

// __constructor (@array &Configuration)
// no returns
// check: ( @void )
// returns true on success, false on failure, or null on locked account.
// login: ( @string username, @string password[, @boolean remember = false] )
// same return as check().
// logout: ( @void )
// returns true at all times.

// TODO: Make a more secure login sessions.


class Login {
    private $prefix = "CoLogin_",
        $user = "",
        $pass = "",
        $c;
    
    public function __construct(&$config) {
        $this->c = &$config;
        $this->cookie_duration = $this->c['login']['duration']*86400;
        $this->landing = $landing;
        session_start()
    }
    
    // Checks cookie login status.
    public function check() {
        // If cookies exist
        if (isset($_COOKIE[$this->prefix . 'user'])) {
            // make session equal to cookie
            $_SESSION[$this->prefix . 'user'] = $_COOKIE[$this->prefix . 'user'];
            $_SESSION[$this->prefix . 'pass'] = $_COOKIE[$this->prefix . 'pass'];
        }
        
        // set username and password
        $this->user = $_SESSION[$this->prefix . 'user'];
        $this->pass = $_SESSION[$this->prefix . 'pass'];
        return $this->authorize();
    }
    
    // Checks login details
    public function login($user, $pass, $remember = false) {
        $this->user = $user;
        $this->pass = md5($this->prefix . $pass);
        
        // If login is unsuccessful
        if (($ret = $this->authorize()) === false)
            return false;
        
        // If remember
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
        session_unset();
        session_destroy();
        return true;
    }
    
    // Authorizes username and password.
    private function authorize() {
        // If user does not exist (empty) or the password does not match the user
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
    
}
?>