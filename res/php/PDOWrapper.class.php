<?php
/**
 * PDOWrapper class
 *
 * Class for handling PDO connection
 *
 * CoreProtect Lookup Web Interface
 * @author Simon Chuu
 * @copyright 2015-2020 Simon Chuu
 * @license MIT License
 * @link https://github.com/chuushi/CoreProtect-Lookup-Web-Interface
 * @since 1.0.0
 */

class PDOWrapper {
    /** @var array */
    private $error, $dbinfo;

    /**
     * PDOWrapper constructor.
     * @param array $dbinfo configuration section for database connection
     */
    public function __construct($dbinfo) {
        if (isset($dbinfo["type"])) {
            if (($dbinfo["type"] == "mysql"
                && isset($dbinfo["host"])
                && isset($dbinfo["database"])
                && isset($dbinfo["username"])
                && isset($dbinfo["password"])
            ) || ($dbinfo["type"] == "sqlite"
                && isset($dbinfo["path"])
            )) {
                $this->dbinfo = $dbinfo;
                return;
            }
        }
        $this->error = [1, "Invalid database config"];
    }

    /**
     * @return PDO|boolean PDO driver that has been initialised
     */
    public function initPDO() {
        if (!isset($this->dbinfo)) {
            $this->error = [1, "Invalid database config"];
            return false;
        }

        try {
            $pdo = ($this->dbinfo["type"] === "mysql")
                ? new PDO("mysql:charset=utf8;host="
                    . $this->dbinfo["host"]
                    . ";dbname="
                    . $this->dbinfo["database"]
                    . $this->dbinfo["flags"],
                    $this->dbinfo["username"],
                    $this->dbinfo["password"],
                    [PDO::ATTR_PERSISTENT => true]
                )
                : new PDO("sqlite:"
                    .$this->dbinfo["path"]
                );
            // Prevent numbers from being quoted (breaks on MySQL)
            $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, FALSE);
            return $pdo;
        } catch(PDOException $ex) {
            $this->error = [$ex->getCode(), $ex->getMessage()];
            return false;
        }
    }

    public function error() {
        return isset($this->error) ? $this->error : null;
    }
}
