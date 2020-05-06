<?php
/**
 * Class StatementPreparer
 */
class StatementPreparer {
    const A_BLOCK_MINE    = 0x0001;
    const A_BLOCK_PLACE   = 0x0002;
    const A_CLICK         = 0x0004;
    const A_KILL          = 0x0008;
    const A_CONTAINER_OUT = 0x0010;
    const A_CONTAINER_IN  = 0x0020;
    const A_CHAT          = 0x0040;
    const A_COMMAND       = 0x0080;
    const A_SESSION       = 0x0100;
    const A_USERNAME      = 0x0200;

    const A_BLOCK_TABLE = self::A_BLOCK_MINE + self::A_BLOCK_PLACE + self::A_CLICK + self::A_KILL;
    const A_CONTAINER_TABLE = self::A_CONTAINER_IN + self::A_CONTAINER_OUT;

    const A_EX_USER       = 0x0400;
    const A_EX_BLOCK      = 0x0800;
    const A_EX_ENTITY     = 0x1000;
    const A_ROLLBACK_YES  = 0x2000;
    const A_ROLLBACK_NO   = 0x4000;
    const A_REV_TIME      = 0x8000;

    const BLOCK = "block";
    const CONTAINER = "container";
    const CHAT = "chat";
    const COMMAND = "command";
    const SESSION = "session";
    const USERNAME = "username";

    const SELECT_BLOCK = "SELECT c.rowid, 'block' AS `table`, c.time, u.user, c.action, w.world, c.x, c.y, c.z, IFNULL(mm.material, em.entity) AS `target`, IFNULL(dm.data, c.data) AS `data`, NULL as `amount`, c.rolled_back";
    const SELECT_CONTAINER = "SELECT c.rowid, 'container' AS `table`, c.time, u.user, c.action, w.world, c.x, c.y, c.z, mm.material AS `target`, c.data, c.amount, c.rolled_back";
    const SELECT_CHAT = "SELECT c.rowid, 'chat' AS `table`, c.time, u.user, NULL as `world`, NULL as `x`, NULL as `y`, NULL as `z`, c.message AS `target`, NULL AS `data`, NULL AS `amount`, NULL AS `rolled_back`";
    const SELECT_COMMAND = "SELECT c.rowid, 'command' AS `table`, c.time, c.user, NULL as `world`, NULL as `x`, NULL as `y`, NULL as `z`, c.message AS `target`, NULL AS `data`, NULL AS `amount`, NULL AS `rolled_back`";
    const SELECT_SESSION = "SELECT c.rowid, 'session' AS `table`, c.time, u.user, c.action, w.world, c.x, c.y, c.z, NULL AS `target`, NULL AS `data`, NULL AS `amount`, NULL AS `rolled_back`";
    const SELECT_USERNAME = "SELECT c.rowid, 'username' AS `table`, c.time, u.user, c.uuid, NULL as `world`, NULL as `x`, NULL as `y`, NULL as `z`, c.user AS target, NULL AS `data`, NULL AS `amount`, NULL AS `rolled_back`";

    const W_MATERIAL = "mm.material";
    const W_ENTITY = "em.entity";
    const W_TIME = "c.time";
    const W_USER = "u.user";
    const W_USER_UUID = "u.uuid";
    const W_WORLD = "w.world";

    const W_COL_XYZ = "xyz";
    const WHERE_XYZ = "x BETWEEN :xyz1 AND :xyz2 AND y BETWEEN :xyz3 AND :xyz4 AND z BETWEEN :xyz5 AND :xyz6";
    const W_COL_ROLLED_BACK = "rollback";
    const WHERE_ROLLED_BACK = "rolled_back= :rlbk";

    /**
     * Input integers
     * @var integer
     */
    private $a, $t, $x, $y, $z, $x2, $y2, $z2, $count;
    /**
     * Input strings
     * @var string
     */
    private $prefix, $u, $b, $e, $w;

    /** @var string[] */
    private $sqlFromWhere, $sqlWhereParts, $sqlPlaceholders;
    private $sqlOrder;

    public function __construct($prefix, $req) {
        $this->prefix = $prefix;
        $this->count  = self::nonnull($req['count'], 25);
        $this->a  = self::nonnull($req['a']);
        $this->b  = self::nonnull($req['b']);
        $this->e  = self::nonnull($req['e']);
        $this->t  = self::nonnull($req['t']);
        $this->u  = self::nonnull($req['u']);
        $this->w  = self::nonnull($req['w']);
        $this->x  = self::nonnull($req['x']);
        $this->x2 = self::nonnull($req['x2']);
        $this->y  = self::nonnull($req['y']);
        $this->y2 = self::nonnull($req['y2']);
        $this->z  = self::nonnull($req['z']);
        $this->z2 = self::nonnull($req['z2']);
    }

    private function nonnull(& $in, $ifunset = null) {
        if (isset($in))
            return $in;
        return $ifunset;
    }

    public function prepareData() {
        $this->populate();

        if (sizeof($this->sqlFromWhere) == 1) {
            $k = array_key_first($this->sqlFromWhere);
            return $this->getSelect($k) . $this->sqlFromWhere[$k] . " ORDER BY c.rowid " . $this->sqlOrder . " LIMIT " . $this->count;
        }

        $queries = [];

        foreach ($this->sqlFromWhere as $table => $from) {
            $queries[] = $this->getSelect($table) . $from . " ORDER BY c.rowid " . $this->sqlOrder . " LIMIT " . $this->count;
        }

        /** @noinspection SqlResolve TODO idk if I should resolve the table schematics */
        return "SELECT * FROM (" . join(" UNION ALL ", $queries) . ") ORDER BY c.time " . $this->sqlOrder . " LIMIT " . $this->count;
    }

    public function prepareCount() {
        $this->populate();

        if (sizeof($this->sqlFromWhere) == 1) {
            $k = array_key_first($this->sqlFromWhere);
            return "SELECT $k AS `table`, COUNT(*) AS `total` " . $this->sqlFromWhere[$k];
        }

        $queries = [];

        foreach ($this->sqlFromWhere as $table => $from) {
            $queries[] = "SELECT $table AS `table`, COUNT(*) AS `total` " . $from;
        }

        return "SELECT * FROM (" . join(" UNION ALL ", $queries) . ")";
    }

    /**
     * @param string $key
     * @return string the appropriate SELECT
     */
    private function getSelect($key) {
        switch ($key) {
            case self::BLOCK:
                return self::SELECT_BLOCK;
            case self::CONTAINER:
                return self::SELECT_CONTAINER;
            case self::CHAT:
                return self::SELECT_CHAT;
            case self::COMMAND:
                return self::COMMAND;
            case self::SESSION:
                return self::SELECT_SESSION;
            case self::USERNAME:
                return self::SELECT_USERNAME;
            default:
                return null;
        }
    }

    /**
     * Populates $this->sqlFromWhere statements along with $this->sqlPlaceholders
     */
    private function populate() {
        if (isset($this->sqlFromWhere))
            return;

        $this->parseWheres();
        $this->sqlFromWhere = [];

        if ($this->a & self::A_BLOCK_TABLE) {
            /** @var string[] $wheres */
            $wheres = [self::W_TIME, self::W_USER, self::W_WORLD, self::W_COL_XYZ, self::W_COL_ROLLED_BACK];
            /** @var string $sql */
            $sql = "FROM `" . $this->prefix . "block` AS c"
                . "LEFT JOIN `" . $this->prefix . "user` AS u ON c.user = u.rowid LEFT JOIN `" . $this->prefix . "world` AS w ON c.wid = w.rowid";

            if ($this->a & (self::A_BLOCK_MINE | self::A_BLOCK_PLACE | self::A_CLICK)) {
                $sql .= "LEFT JOIN `" . $this->prefix . "material_map` AS mm ON c.action<>3 AND c.type = mm.rowid LEFT JOIN `" . $this->prefix . "blockdata_map` AS dm ON c.action<>3 AND c.data = dm.rowid";
                $wheres[] = self::W_MATERIAL;
            }
            if ($this->a & self::A_KILL) {
                $sql .= "LEFT JOIN `" . $this->prefix . "entity_map` AS em ON c.action=3 AND c.type = em.rowid";
                $wheres[] = self::W_ENTITY;
            }

            $this->sqlFromWhere[self::BLOCK] = $sql . $this->generateWhere($wheres);
        }
        if ($this->a & self::A_CONTAINER_TABLE) {
            /** @var string[] $wheres */
            $wheres = [self::W_TIME, self::W_USER, self::W_WORLD, self::W_COL_XYZ, self::W_COL_ROLLED_BACK, self::W_MATERIAL];
            /** @var string $sql */
            $sql = "FROM `" . $this->prefix."container` AS c"
                . "LEFT JOIN `" . $this->prefix . "user` AS u ON c.user = u.rowid LEFT JOIN `" . $this->prefix . "world` AS w ON c.wid = w.rowid"
                . "LEFT JOIN `" . $this->prefix . "material_map` AS mm ON c.action<>3 AND c.type = mm.rowid LEFT JOIN `" . $this->prefix."blockdata_map` AS dm ON c.action<>3 AND c.data = dm.rowid";

            $this->sqlFromWhere[self::CONTAINER] = $sql . $this->generateWhere($wheres);
        }

        if ($this->a & self::A_CHAT) {
            /** @var string[] $wheres */
            $wheres = [self::W_TIME, self::W_USER];
            /** @var string $sql */
            $sql = "FROM `" . $this->prefix . "chat` AS c"
                . "LEFT JOIN `" . $this->prefix."user` AS u ON c.user = u.rowid";

            $this->sqlFromWhere[self::CHAT] = $sql . $this->generateWhere($wheres);
        }

        if ($this->a & self::A_COMMAND) {
            /** @var string[] $wheres */
            $wheres = [self::W_TIME, self::W_USER];
            /** @var string $sql */
            $sql = "FROM `" . $this->prefix."command` AS c"
                . "LEFT JOIN `" . $this->prefix . "user` AS u ON c.user = u.rowid";

            $this->sqlFromWhere[self::COMMAND] = $sql . $this->generateWhere($wheres);
        }

        if ($this->a & self::A_SESSION) {
            /** @var string[] $wheres */
            $wheres = [self::W_TIME, self::W_USER, self::W_WORLD, self::W_COL_XYZ];
            /** @var string $sql */
            $sql = "FROM `" . $this->prefix . "session` AS c"
                . "LEFT JOIN `" . $this->prefix . "user` AS u ON c.user = u.rowid LEFT JOIN `" . $this->prefix . "world` AS w ON c.wid = w.rowid";

            $this->sqlFromWhere[self::SESSION] = $sql . $this->generateWhere($wheres);
        }

        if ($this->a & self::A_USERNAME) {
            /** @var string[] $wheres */
            $wheres = [self::W_TIME, self::W_USER];
            /** @var string $sql */
            $sql = "FROM `".$this->prefix . "username_log` AS c"
                . "LEFT JOIN `co2_user` AS u ON c.uuid = u.uuid";

            $this->sqlFromWhere[self::USERNAME] = $sql . $this->generateWhere($wheres);
        }
    }

    /**
     * @param string[] $columns
     * @param string[] $additional Additional
     * @return string pre-spaced at the beginning
     */
    private function generateWhere($columns, $additional = null) {
        $wheres = $additional == null ? [] : $additional;
        foreach ($columns as $col) {
            if ($this->sqlWhereParts[$col]) {
                $wheres[] = $this->sqlWhereParts[$col];
            }
        }
        if (sizeof($wheres) == 0)
            return "";
        return " WHERE " . join(" AND ", $wheres);
    }

    private function parseWheres() {
        $this->sqlWhereParts = [];
        $this->sqlPlaceholders = [];

        if ($this->b != null)
            self::whereAbsoluteString(self::W_MATERIAL, $this->a & self::A_EX_BLOCK, "blk");
        if ($this->e != null)
            self::whereAbsoluteString(self::W_ENTITY, $this->a & self::A_EX_ENTITY, "ety");
        if ($this->u != null)
            self::whereAbsoluteString(self::W_USER, $this->a & self::A_EX_USER, "usr", self::W_USER_UUID);
        if ($this->w != null)
            self::whereAbsoluteString(self::W_WORLD, false, "wld");
        if ($this->t != null) {
            if ($this->a & self::A_REV_TIME) {
                $this->sqlWhereParts[self::W_TIME] = self::W_TIME . ">= :time";
                $this->sqlOrder = "ASC";
            } else {
                $this->sqlWhereParts[self::W_TIME] = self::W_TIME . "<= :time";
                $this->sqlOrder = "DESC";
            }
            $this->sqlPlaceholders[":time"] = $this->t;
        } else {
            $this->sqlOrder = "DESC";
        }
        if ($this->x != null && $this->y != null && $this->z != null && $this->x2 != null && $this->y2 != null && $this->z2 != null) {
            $this->sqlWhereParts[self::W_COL_XYZ] = self::WHERE_XYZ;
            $this->sqlPlaceholders[":xyz1"] = $this->x;
            $this->sqlPlaceholders[":xyz2"] = $this->x2;
            $this->sqlPlaceholders[":xyz3"] = $this->y;
            $this->sqlPlaceholders[":xyz4"] = $this->y2;
            $this->sqlPlaceholders[":xyz5"] = $this->z;
            $this->sqlPlaceholders[":xyz6"] = $this->z2;
        }
        if ($this->a & (self::A_ROLLBACK_YES | self::A_ROLLBACK_NO)) {
            $this->sqlWhereParts[self::W_COL_ROLLED_BACK] = self::WHERE_ROLLED_BACK;
            $this->sqlPlaceholders[":rlbk"] = $this->a & self::A_ROLLBACK_YES ? 1 : 0;
        }
    }

    private function whereAbsoluteString($column, $exFlag, $prefix, $uuidColumn = null) {
        $parts = [];
        if ($exFlag)
            $d = "<>";
        else
            $d = "=";
        foreach (str_getcsv($this->b) as $k => $val) {
            if ($uuidColumn && strlen($val) == 36) {
                $parts[] = $uuidColumn . $d . " :$prefix$k";
                $this->sqlPlaceholders[":$prefix$k"] = $val;
            } else {
                $parts[] = $column . $d . " :$prefix$k";
                $this->sqlPlaceholders[":$prefix$k"] = $val;
            }
        }
        if (sizeof($parts) == 1)
            $this->sqlWhereParts[$column] = $parts[0];
        else
            $this->sqlWhereParts[$column] = "(" . join(" OR ", $parts) . ")";
    }
}