<?php
/**
 * Session class
 *
 * Class for handling user login sessions
 *
 * CoreProtect Lookup Web Interface
 * @author Simon Chuu
 * @copyright 2015-2020 Simon Chuu
 * @license MIT License
 * @link https://github.com/chuushi/CoreProtect-Lookup-Web-Interface
 * @since 1.0.0
 */

class Session {
    const COOKIE_DURATION = 604800; // 1 week
    const PREFIX = "coLogin_";
    const USERNAME = self::PREFIX . "username";
    const PASSWORD = self::PREFIX . "password";
    /** @var array */
    private $config;

    /** @var string */
    private $username = null, $password = null;
    /** @var boolean */
    private $valid, $isAdmin;

    public function __construct(&$config) {
        $this->config = &$config;
        session_start();

        // If session isn't set but the cookies exist
        if (!isset($_SESSION[self::USERNAME])) {
            if (isset($_COOKIE[self::USERNAME])) {
                $this->username = $_COOKIE[self::USERNAME];
                $this->password = $_COOKIE[self::PASSWORD];

                if ($this->validCredential()) {
                    // make session equal to cookie
                    $_SESSION[self::USERNAME] = $this->username;
                    $_SESSION[self::PASSWORD] = $this->password;

                    // reset cookie expiry
                    setcookie(self::USERNAME, $this->username, time() + self::COOKIE_DURATION);
                    setcookie(self::PASSWORD, $this->password, time() + self::COOKIE_DURATION);
                } else {
                    // unset cookie
                    $this->logout();
                }
            }
        } else {
            $this->username = $_SESSION[self::USERNAME];
            $this->password = $_SESSION[self::PASSWORD];
        }
    }

    public function getUsername() {
        return $this->validCredential() ? $this->username : null;
    }

    public function hasLookupAccess() {
        return $this->validCredential() || $this->config['user'][1] === '';
    }

    public function validCredential() {
        if (isset($this->valid))
            return $this->valid;

        if (strcasecmp($this->username, $this->config['administrator'][0]) === 0
                && password_verify($this->config['administrator'][1], $this->password)) {
            $this->username = $this->config['administrator'][0];
            $this->valid = true;
            $this->isAdmin = true;
        } elseif (strcasecmp($this->username, $this->config['user'][0]) === 0
                && password_verify($this->config['user'][1], $this->password)) {
            $this->username = $this->config['user'][0];
            $this->valid = true;
            $this->isAdmin = false;
        } else {
            $this->valid = false;
            $this->isAdmin = false;
        }

        return $this->valid;
    }

    public function login($username, $password, $remember = false) {
        if ($password == '')
            return false;

        $this->username = $username;
        $this->password = password_hash($password, PASSWORD_DEFAULT);
        
        // If login is unsuccessful
        if (!$this->validCredential())
            return false;

        $_SESSION[self::USERNAME] = $this->username;
        $_SESSION[self::PASSWORD] = $this->password;

        if ($remember) {
            setcookie(self::USERNAME, $this->username, time() + self::COOKIE_DURATION);
            setcookie(self::PASSWORD, $this->password, time() + self::COOKIE_DURATION);
        }

        return true;
    }

    public function logout() {
        if (isset($_COOKIE[self::USERNAME])) setcookie(self::USERNAME, "", time() - 3600);
        if (isset($_COOKIE[self::PASSWORD])) setcookie(self::PASSWORD, "", time() - 3600);
        unset($_SESSION[self::USERNAME]);
        unset($_SESSION[self::PASSWORD]);
        $this->username = null;
        $this->password = null;
    }
}
