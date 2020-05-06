<?php
// CoLWI v0.9.3
// pdoWrapper PHP Function
// Copyright (c) 2015-2016 SimonOrJ

// pdoWrapper (array database)
//   returns PDO on success or PDOException on failure.

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
                    $this->dbinfo["user"],
                    $this->dbinfo["pass"],
                    [PDO::ATTR_PERSISTENT => true]
                )
                : new PDO("sqlite:"
                    .$this->dbinfo["path"]
                );
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
