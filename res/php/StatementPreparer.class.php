<?php
/**
 * StatementPreparer class
 *
 * Class for preparing an SQL statement with corresponding parameters
 *
 * CoreProtect Lookup Web Interface
 * @author Simon Chuu
 * @copyright 2015-2020 Simon Chuu
 * @license MIT License
 * @link https://github.com/chuushi/CoreProtect-Lookup-Web-Interface
 * @since 1.0.0
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

    const A_BLOCK_MATERIAL = self::A_BLOCK_MINE | self::A_BLOCK_PLACE | self::A_CLICK;
    const A_BLOCK_ENTITY = self::A_KILL;
    const A_BLOCK_TABLE = self::A_BLOCK_MATERIAL | self::A_KILL;
    const A_CONTAINER_TABLE = self::A_CONTAINER_IN | self::A_CONTAINER_OUT;

    const A_WHERE_MATERIAL = self::A_BLOCK_MATERIAL | self::A_CONTAINER_TABLE | self::A_SESSION;
    const A_WHERE_ENTITY = self::A_BLOCK_ENTITY;
    const A_WHERE_COORDS = self::A_BLOCK_TABLE | self::A_CONTAINER_TABLE | self::A_SESSION;
    const A_WHERE_ROLLBACK = self::A_BLOCK_MINE | self::A_BLOCK_PLACE | self::A_KILL | self::A_CONTAINER_TABLE;
    const A_WHERE_KEYWORD = self::A_CHAT | self::A_COMMAND | self::A_USERNAME;

    const A_LOOKUP_TABLE = self::A_BLOCK_TABLE | self::A_CONTAINER_TABLE | self::A_CHAT | self::A_COMMAND | self::A_SESSION | self::A_USERNAME;

    const A_EX_USER       = 0x0400;
    const A_EX_BLOCK      = 0x0800;
    const A_EX_ENTITY     = 0x1000;
    const A_EX_WORLD      = 0x2000;
    const A_ROLLBACK_YES  = 0x4000;
    const A_ROLLBACK_NO   = 0x8000;
    const A_REV_TIME      = 0x10000;

    const BLOCK = "block";
    const CONTAINER = "container";
    const CHAT = "chat";
    const COMMAND = "command";
    const SESSION = "session";
    const USERNAME = "username";

    const FILTER_MATERIAL = 1;
    const FILTER_ENTITY = 2;
    const FILTER_USER = 3;
    const FILTER_WORLD = 4;
    const FILTER_TIME = 5;

    const W_MATERIAL_ID = 'mm.id';
    const W_ENTITY_ID = 'em.id';
    const W_USER_ID = 'u.rowid';
    const W_USER_ENTITY_ID = 'um.rowid';
    const W_WORLD_ID = 'w.id';

    const T_MATERIAL_ID = 'c.type';
    const T_ENTITY_ID = 'c.type';
    const T_USER_ID = 'c.user';
    const T_USER_ENTITY_ID = 'c.data';
    const T_WORLD_ID = 'c.wid';

    const W_MATERIAL = "mm.material IN";
    const W_ENTITY = "em.entity IN";
    const W_USER = "u.user IN";
    const W_USER_UUID = "u.uuid IN";
    const W_USER_ENTITY = "um.user IN";
    const W_USER_ENTITY_UUID = "um.uuid IN";
    const W_WORLD = "w.world IN";
    const W_TIME = 'c.time';

    const W_COL_XYZ = "xyz";
    const WHERE_XYZ = "x BETWEEN :xyz1 AND :xyz2 AND y BETWEEN :xyz3 AND :xyz4 AND z BETWEEN :xyz5 AND :xyz6";
    const W_COL_ROLLED_BACK = "rollback";
    const WHERE_ROLLED_BACK = "rolled_back= :rlbk";

    const W_KEYWORD_MESSAGE = "c.message";
    const W_KEYWORD_USER = "c.user";

    /**
     * Input integers
     * @var integer
     */
    private $a, $t, $x, $y, $z, $x2, $y2, $z2, $count, $offset;
    /**
     * Input strings
     * @var string
     */
    private $prefix, $u, $b, $e, $w, $keyword;

    /** @var string[] */
    private $sqlFromWhere, $sqlWhereParts, $sqlPlaceholders;
    private $sqlOrder;

    public function __construct($prefix, & $req, $count, $moreCount) {
        $this->prefix = $prefix;
        $this->offset = self::nonnullInt($req['offset'], 0);
        $this->count  = self::nonnullInt($req['count'], $this->offset == null ? $count : $moreCount);
        $this->a  = self::nonnull($req['a']);
        $this->b  = self::nonnull($req['b']);
        $this->e  = self::nonnull($req['e']);
        $this->t  = self::nonnullInt($req['t']);
        $this->u  = self::nonnull($req['u']);
        $this->w  = self::nonnull($req['w']);
        $this->x  = self::nonnullInt($req['x']);
        $this->x2 = self::nonnullInt($req['x2']);
        $this->y  = self::nonnullInt($req['y']);
        $this->y2 = self::nonnullInt($req['y2']);
        $this->z  = self::nonnullInt($req['z']);
        $this->z2 = self::nonnullInt($req['z2']);
        $this->keyword = self::nonnull($req['keyword']);
    }

    private function nonnull(& $in) {
        if (isset($in) && $in !== "")
            return $in;
        return null;
    }

    private function nonnullInt(& $in, $ifunset = null) {
        if (isset($in) && $in !== "")
            return intval($in);
        return $ifunset;
    }

    public function preparedStatementData() {
        $this->populate();

        if (sizeof($this->sqlFromWhere) == 0)
            return "";

        if (sizeof($this->sqlFromWhere) == 1) {
            $v = reset($this->sqlFromWhere);
            $k = key($this->sqlFromWhere);
            return $this->getSelect($k) . " " . $v . " ORDER BY c.rowid " . $this->sqlOrder . " LIMIT :offset, :count";
        }

        $queries = [];

        foreach ($this->sqlFromWhere as $table => $from) {
            $queries[] = $this->getSelect($table) . " " . $from;
        }


        $ret = "";
        foreach($queries as $key => $val) {
            if($key) $ret .= " UNION ALL ";
            $ret .= "SELECT * FROM (" . $val . " ORDER BY c.rowid " . $this->sqlOrder . " LIMIT :osct) AS t".$key;
        }

        return $ret . " ORDER BY time " . $this->sqlOrder . " LIMIT :offset, :count";
    }

    public function preparedStatementCount() {
        $this->populate();

        if (sizeof($this->sqlFromWhere) == 0)
            return "";

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

    public function preparedParams() {
        $this->populate();
        return $this->sqlPlaceholders;
    }

    /**
     * @param string $key
     * @return string the appropriate SELECT
     */
    private function getSelect($key) {
        switch ($key) {
            case self::BLOCK:
                $material = $this->a & self::A_BLOCK_MATERIAL;
                $entity = $this->a & self::A_BLOCK_ENTITY;
                return "SELECT c.rowid, 'block' AS `table`, c.time, u.user, u.uuid, c.action, w.world, c.x, c.y, c.z, "
                    . (
                        $material && $entity
                            ? "IFNULL(mm.material, IFNULL(em.entity, um.user))"
                            : ($material ? "mm.material" : "IFNULL(em.entity, um.user)")
                    )
                    . " AS `target`, "
                    . (
                        $material && $entity
                            ? "IFNULL(dm.data, IFNULL(um.uuid, c.data))"
                            : ($material ? "IFNULL(dm.data, c.data)" : "IFNULL(um.uuid, c.data)")
                    )
                    . " AS `data`, NULL as `amount`, c.rolled_back";
            case self::CONTAINER:
                return "SELECT c.rowid, 'container' AS `table`, c.time, u.user, u.uuid, c.action, w.world, c.x, c.y, c.z, mm.material AS `target`, c.data, c.amount, c.rolled_back";
            case self::CHAT:
                return "SELECT c.rowid, 'chat' AS `table`, c.time, u.user, u.uuid, NULL as `action`, NULL as `world`, NULL as `x`, NULL as `y`, NULL as `z`, c.message AS `target`, NULL AS `data`, NULL AS `amount`, NULL AS `rolled_back`";
            case self::COMMAND:
                return "SELECT c.rowid, 'command' AS `table`, c.time, u.user, u.uuid, NULL as `action`, NULL as `world`, NULL as `x`, NULL as `y`, NULL as `z`, c.message AS `target`, NULL AS `data`, NULL AS `amount`, NULL AS `rolled_back`";
            case self::SESSION:
                return "SELECT c.rowid, 'session' AS `table`, c.time, u.user, u.uuid, c.action, w.world, c.x, c.y, c.z, NULL AS `target`, NULL AS `data`, NULL AS `amount`, NULL AS `rolled_back`";
            case self::USERNAME:
                return "SELECT c.rowid , 'username' AS `table`, c.time, u.user, c.uuid, NULL as `action`, NULL as `world`, NULL as `x`, NULL as `y`, NULL as `z`, c.user AS target, NULL AS `data`, NULL AS `amount`, NULL AS `rolled_back`";
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

        $this->sqlFromWhere = [];

        if ($this->a & self::A_LOOKUP_TABLE == 0) {
            $this->sqlPlaceholders = [];
            return;
        }

        $this->parseWheres();
        $this->sqlPlaceholders[":count"] = $this->count;
        $this->sqlPlaceholders[":offset"] = $this->offset;


        if ($this->a & self::A_BLOCK_TABLE) {
            /** @var string[] $wheres */
            $wheres = [self::FILTER_TIME, self::FILTER_USER, self::FILTER_WORLD, self::W_COL_XYZ, self::W_COL_ROLLED_BACK];
            /** @var string $sql */
            $sql = "FROM `" . $this->prefix . "block` AS c"
                . " LEFT JOIN `" . $this->prefix . "user` AS u ON c.user = u.rowid LEFT JOIN `" . $this->prefix . "world` AS w ON c.wid = w.rowid";

            if ($this->a & (self::A_BLOCK_MATERIAL)) {
                $sql .= " LEFT JOIN `" . $this->prefix . "material_map` AS mm ON c.action<>3 AND c.type = mm.rowid LEFT JOIN `" . $this->prefix . "blockdata_map` AS dm ON c.action<>3 AND c.data = dm.rowid";
                $wheres[] = self::FILTER_MATERIAL;
            }
            if ($this->a & self::A_KILL) {
                $sql .= " LEFT JOIN `" . $this->prefix . "entity_map` AS em ON c.action=3 AND c.type<>0 AND c.type = em.rowid";
                $sql .= " LEFT JOIN `" . $this->prefix . "user` AS um ON c.action=3 AND c.type=0 AND c.data = um.rowid";
                $wheres[] = self::FILTER_ENTITY;
            }

            // If action=0, 1, 2, and 3 are not on at the same time
            $a = null;
            if (($this->a & self::A_BLOCK_TABLE) != self::A_BLOCK_TABLE) {
                $aList = [];
                if ($this->a & self::A_BLOCK_MINE)
                    $aList[] = "0";
                if ($this->a & self::A_BLOCK_PLACE)
                    $aList[] = "1";
                if ($this->a & self::A_CLICK)
                    $aList[] = "2";
                if ($this->a & self::A_KILL)
                    $aList[] = "3";
                $a = "c.action IN (" . join(",", $aList) . ")";
            }

            $this->sqlFromWhere[self::BLOCK] = $sql . $this->generateWhere($wheres, $a);
        }
        if ($this->a & self::A_CONTAINER_TABLE) {
            /** @var string[] $wheres */
            $wheres = [self::FILTER_TIME, self::FILTER_USER, self::FILTER_WORLD, self::W_COL_XYZ, self::W_COL_ROLLED_BACK, self::FILTER_MATERIAL];
            /** @var string $sql */
            $sql = "FROM `" . $this->prefix."container` AS c"
                . " LEFT JOIN `" . $this->prefix . "user` AS u ON c.user = u.rowid LEFT JOIN `" . $this->prefix . "world` AS w ON c.wid = w.rowid"
                . " LEFT JOIN `" . $this->prefix . "material_map` AS mm ON c.action<>3 AND c.type = mm.rowid LEFT JOIN `" . $this->prefix."blockdata_map` AS dm ON c.action<>3 AND c.data = dm.rowid";

            $a = null;
            if (($this->a & self::A_CONTAINER_TABLE) != self::A_CONTAINER_TABLE) {
                if ($this->a & self::A_CONTAINER_OUT)
                    $a = "c.action=0";
                if ($this->a & self::A_CONTAINER_IN)
                    $a = "c.action=1";
            }

            $this->sqlFromWhere[self::CONTAINER] = $sql . $this->generateWhere($wheres, $a);
        }

        if ($this->a & self::A_CHAT) {
            /** @var string[] $wheres */
            $wheres = [self::FILTER_TIME, self::FILTER_USER, self::W_KEYWORD_MESSAGE];
            /** @var string $sql */
            $sql = "FROM `" . $this->prefix . "chat` AS c"
                . " LEFT JOIN `" . $this->prefix."user` AS u ON c.user = u.rowid";

            $this->sqlFromWhere[self::CHAT] = $sql . $this->generateWhere($wheres);
        }

        if ($this->a & self::A_COMMAND) {
            /** @var string[] $wheres */
            $wheres = [self::FILTER_TIME, self::FILTER_USER, self::W_KEYWORD_MESSAGE];
            /** @var string $sql */
            $sql = "FROM `" . $this->prefix."command` AS c"
                . " LEFT JOIN `" . $this->prefix . "user` AS u ON c.user = u.rowid";

            // TODO: Keyword

            $this->sqlFromWhere[self::COMMAND] = $sql . $this->generateWhere($wheres);
        }

        if ($this->a & self::A_SESSION) {
            /** @var string[] $wheres */
            $wheres = [self::FILTER_TIME, self::FILTER_USER, self::FILTER_WORLD, self::W_COL_XYZ];
            /** @var string $sql */
            $sql = "FROM `" . $this->prefix . "session` AS c"
                . " LEFT JOIN `" . $this->prefix . "user` AS u ON c.user = u.rowid LEFT JOIN `" . $this->prefix . "world` AS w ON c.wid = w.rowid";

            $this->sqlFromWhere[self::SESSION] = $sql . $this->generateWhere($wheres);
        }

        if ($this->a & self::A_USERNAME) {
            /** @var string[] $wheres */
            $wheres = [self::FILTER_TIME, self::FILTER_USER, self::W_KEYWORD_USER];
            /** @var string $sql */
            $sql = "FROM `".$this->prefix . "username_log` AS c"
                . " LEFT JOIN `".$this->prefix . "user` AS u ON c.uuid = u.uuid";

            // TODO: Keyword

            $this->sqlFromWhere[self::USERNAME] = $sql . $this->generateWhere($wheres);
        }
        if (count($this->sqlFromWhere) !== 1)
            $this->sqlPlaceholders[":osct"] = $this->offset + $this->count;
    }

    /**
     * @param string[] $columns
     * @param string $additional Additional
     * @return string pre-spaced at the beginning
     */
    private function generateWhere($columns, $additional = null) {
        $wheres = $additional == null ? [] : [$additional];
        $me = 0;
        foreach ($columns as $col) {
            if (isset($this->sqlWhereParts[$col])) {
                if ($col == self::FILTER_MATERIAL)
                    $me |= 0b01;
                elseif ($col == self::FILTER_ENTITY)
                    $me |= 0b10;
                else
                    $wheres[] = $this->sqlWhereParts[$col];
            }
        }

        if ($me == 0b11) {
            $wheres[] = "(" . $this->sqlWhereParts[self::FILTER_MATERIAL] . " OR " . $this->sqlWhereParts[self::FILTER_ENTITY] . ")";
        } elseif ($me & 0b01) {
            $wheres[] = $this->sqlWhereParts[self::FILTER_MATERIAL];
        } elseif ($me & 0b10) {
            $wheres[] = $this->sqlWhereParts[self::FILTER_ENTITY];
        }

        if (sizeof($wheres) == 0)
            return "";
        return " WHERE " . join(" AND ", $wheres);
    }

    private function parseWheres() {
        $this->sqlWhereParts = [];
        $this->sqlPlaceholders = [];

        if (($this->a & self::A_WHERE_MATERIAL) && !empty($this->b))
            self::whereAbsoluteString(self::FILTER_MATERIAL, $this->b, $this->a & self::A_EX_BLOCK, "blk");
        if (($this->a & self::A_WHERE_ENTITY) && !empty($this->e))
            self::whereAbsoluteString(self::FILTER_ENTITY, $this->e, $this->a & self::A_EX_ENTITY, "ety");
        if (($this->a & self::A_WHERE_COORDS) && !empty($this->w))
            self::whereAbsoluteString(self::FILTER_WORLD, $this->w, $this->a & self::A_EX_WORLD, "wld");
        if (!empty($this->u))
            self::whereAbsoluteString(self::FILTER_USER, $this->u, $this->a & self::A_EX_USER, "usr");
        if ($this->t !== null) {
            if ($this->a & self::A_REV_TIME) {
                $this->sqlWhereParts[self::FILTER_TIME] = self::W_TIME . ">= :time";
                $this->sqlOrder = "ASC";
            } else {
                $this->sqlWhereParts[self::FILTER_TIME] = self::W_TIME . "<= :time";
                $this->sqlOrder = "DESC";
            }
            $this->sqlPlaceholders[":time"] = $this->t;
        } else {
            $this->sqlOrder = $this->a & self::A_REV_TIME ? "ASC" : "DESC";
        }

        if ($this->a & self::A_WHERE_COORDS && $this->x != null && $this->y != null && $this->z != null && $this->x2 != null && $this->y2 != null && $this->z2 != null) {
            $this->sqlWhereParts[self::W_COL_XYZ] = self::WHERE_XYZ;
            $this->sqlPlaceholders[":xyz1"] = $this->x;
            $this->sqlPlaceholders[":xyz2"] = $this->x2;
            $this->sqlPlaceholders[":xyz3"] = $this->y;
            $this->sqlPlaceholders[":xyz4"] = $this->y2;
            $this->sqlPlaceholders[":xyz5"] = $this->z;
            $this->sqlPlaceholders[":xyz6"] = $this->z2;
        }
        if ($this->a & self::A_WHERE_ROLLBACK && $this->a & (self::A_ROLLBACK_YES | self::A_ROLLBACK_NO)) {
            $this->sqlWhereParts[self::W_COL_ROLLED_BACK] = self::WHERE_ROLLED_BACK;
            $this->sqlPlaceholders[":rlbk"] = $this->a & self::A_ROLLBACK_YES ? 1 : 0;
        }

        if ($this->a & self::A_WHERE_KEYWORD && $this->keyword != null)
            $this->whereKeywordSearch();
    }

    private function whereAbsoluteString($column, $query, $exFlag, $prefix) {
        $names = [];
        $uuids = [];
        $users = [];

        if ($column === self::FILTER_ENTITY || $column === self::FILTER_USER) {
            foreach (str_getcsv($query) as $k => $uncleanVal) {
                $val = trim($uncleanVal);

                if (strlen($val) == 36) { // TODO: Make sure $val is an actual UUID
                    $uuids[] = ":$prefix$k";
                    $this->sqlPlaceholders[":$prefix$k"] = $val;
                } else {
                    $names[] = ":$prefix$k";
                    if ($column === self::FILTER_ENTITY) $users[] = ":$prefix$k";
                    $this->sqlPlaceholders[":$prefix$k"] = $val;
                }
            }
        } else {
            foreach (str_getcsv($query) as $k => $uncleanVal) {
                $names[] = ":$prefix$k";
                $this->sqlPlaceholders[":$prefix$k"] = trim($uncleanVal);
            }
        }

        switch ($column) {
            case self::FILTER_MATERIAL:
                $tableId = self::T_MATERIAL_ID;
                $in = self::W_MATERIAL;
                $selectId = self::W_MATERIAL_ID;
                break;
            case self::FILTER_ENTITY:
                $tableId = self::T_ENTITY_ID;
                $tableId2 = self::T_USER_ENTITY_ID;
                $in = self::W_ENTITY;
                $inUser = self::W_USER_ENTITY;
                $inUuid = self::W_USER_ENTITY_UUID;
                $selectId = self::W_ENTITY_ID;
                $selectId2 = self::W_USER_ENTITY_ID;
                break;
            case self::FILTER_USER:
                $tableId = self::T_USER_ID;
                $in = self::W_USER;
                $inUuid = self::W_USER_UUID;
                $selectId = self::W_USER_ID;
                break;
            case self::FILTER_WORLD:
                $tableId = self::T_WORLD_ID;
                $in = self::W_WORLD;
                $selectId = self::W_WORLD_ID;
                break;
            default:
                return;
        }

        if (sizeof($names)) {
            if ($column === self::FILTER_USER) {
                // Username and UUID
                // logic: if one is not set, the other is set.
                if (!sizeof($uuids))
                    $whereIn = $in . '(' . join(',', $names) . ')';
                elseif (!sizeof($names))
                    $whereIn = $inUuid . '(' . join(',', $uuids) . ')';
                else
                    $whereIn = $in . '(' . join(',', $names) . ') OR ' . $inUuid . '(' . join(',', $uuids) . ')';
            } else {
                $whereIn = $in . '(' . join(',', $names) . ')';
            }

            $add = $this->selectIdWhere($tableId, $selectId, $whereIn, $exFlag);
        }

        if (sizeof($users) || $column === self::FILTER_ENTITY && sizeof($uuids)) {
            if (!sizeof($uuids))
                $whereIn2 = $inUser . '(' . join(',', $users) . ')';
            elseif (sizeof($users))
                $whereIn2 = $inUuid . '(' . join(',', $uuids) . ')';
            else
                $whereIn2 = $inUser . '(' . join(',', $users) . ') OR ' . $inUuid . '(' . join(',', $uuids) . ')';

            $add2 = $this->selectIdWhere($tableId2, $selectId2, $whereIn2, $exFlag);
            if (isset($add))
                $add .= ($exFlag ? ' AND ' : ' OR ') . $add2;
            else
                $add = $add2;
        }

        if (isset($add))
            $this->sqlWhereParts[$column] = $add;
    }

    private function selectIdWhere($tableId, $mapId, $whereIn, $exFlag) {
        return $tableId. ($exFlag ? ' NOT ' : ' ') . "IN (SELECT $mapId WHERE $whereIn)";
    }

    private function whereKeywordSearch() {
        $msgParts = [];
        $usrParts = [];
        foreach (str_getcsv($this->keyword) as $k => $uncleanVal) {
            $val = trim($uncleanVal);
            $msgParts[] = self::W_KEYWORD_MESSAGE . " LIKE :kwd$k";
            $usrParts[] = self::W_KEYWORD_USER . " LIKE :kwd$k";
            $this->sqlPlaceholders[":kwd$k"] = "%$val%";
        }
            $this->sqlWhereParts[self::W_KEYWORD_MESSAGE] = sizeof($msgParts) == 1
                ? $msgParts[0] : "(" . join(" AND ", $msgParts) . ")";
            $this->sqlWhereParts[self::W_KEYWORD_USER] = sizeof($msgParts) == 1
                ? $usrParts[0] : "(" . join(" AND ", $usrParts) . ")";
    }
}