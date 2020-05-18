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
class StatementPreparer
{
    const FLAG_PRE_BLOCK_NAME = 0x01;
    const FLAG_USE_BLOCKDATA_TABLE_YES = 0x02;
    const FLAG_USE_BLOCKDATA_TABLE_NO = 0x04;

    const FLAG_USE_BLOCKDATA_TABLE_DEFINED = self::FLAG_USE_BLOCKDATA_TABLE_YES | self::FLAG_USE_BLOCKDATA_TABLE_NO;

    const A_BLOCK_MINE = 0x0001;
    const A_BLOCK_PLACE = 0x0002;
    const A_CLICK = 0x0004;
    const A_KILL = 0x0008;
    const A_CONTAINER_OUT = 0x0010;
    const A_CONTAINER_IN = 0x0020;
    const A_CHAT = 0x0040;
    const A_COMMAND = 0x0080;
    const A_SESSION = 0x0100;
    const A_USERNAME = 0x0200;

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

    const A_EX_USER = 0x0400;
    const A_EX_BLOCK = 0x0800;
    const A_EX_ENTITY = 0x1000;
    const A_EX_WORLD = 0x2000;
    const A_ROLLBACK_YES = 0x4000;
    const A_ROLLBACK_NO = 0x8000;
    const A_REV_TIME = 0x10000;

    const BLOCK = 1;
    const CONTAINER = 2;
    const CHAT = 3;
    const COMMAND = 4;
    const SESSION = 5;
    const USERNAME = 6;
    const WORLD_MAP = 16;
    const USER_MAP = 17;
    const MATERIAL_MAP = 18;
    const ENTITY_MAP = 19;

    const FILTER_LIMIT = 0;
    const FILTER_MATERIAL = 1;
    const FILTER_ENTITY = 2;
    const FILTER_USER = 3;
    const FILTER_WORLD = 4;
    const FILTER_TIME = 5;
    const FILTER_COORDS = 6;
    const FILTER_ROLLBACK = 7;
    const FILTER_KEYWORD_MESSAGE = 8;
    const FILTER_KEYWORD_USER = 9;
    const FILTER_LIMIT_SUM = 10;

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

    const WHERE_XYZ = "x BETWEEN ? AND ? AND y BETWEEN ? AND ? AND z BETWEEN ? AND ?";
    const WHERE_ROLLED_BACK = "rolled_back= ?";

    const W_KEYWORD_MESSAGE = "c.message";
    const W_KEYWORD_USER = "c.user";

    /**
     * Input booleans
     * @var boolean
     */
    private $useBlockdata;
    /**
     * Input integers
     * @var integer
     */
    private $a, $t, $x, $y, $z, $x2, $y2, $z2, $count, $offset;
    /**
     * Input strings
     * @var string
     */
    private $prefix;
    /**
     * Input arrays from csv strings
     * @var string[]
     */
    private $u, $b, $e, $w, $keyword;

    /** @var string[] */
    private $sqlFromWhere, $sqlWhereParts, $fromWhereParamFilters, $sqlParams = [];
    /** @var string[][] */
    private $whereParams;
    /** @var string */
    private $sqlOrder;

    public function __construct($prefix, & $req, $count, $moreCount, $flag = 0) {
        $this->prefix = $prefix;
        $this->offset = self::nonnullInt($req['offset'], 0);
        $this->count = self::nonnullInt($req['count'], $this->offset == null ? $count : $moreCount);
        $this->a = self::nonnullInt($req['a']);
        $this->b = self::nonnullArr($req['b']);
        $this->e = self::nonnullArr($req['e']);
        $this->t = self::nonnullInt($req['t']);
        $this->u = self::nonnullArr($req['u']);
        $this->w = self::nonnullArr($req['w']);
        $this->x = self::nonnullInt($req['x']);
        $this->x2 = self::nonnullInt($req['x2']);
        $this->y = self::nonnullInt($req['y']);
        $this->y2 = self::nonnullInt($req['y2']);
        $this->z = self::nonnullInt($req['z']);
        $this->z2 = self::nonnullInt($req['z2']);
        $this->keyword = self::nonnullArr($req['keyword'], false);
        $this->useBlockdata = ($flag & self::FLAG_USE_BLOCKDATA_TABLE_YES) !== 0;

        if ($flag & self::FLAG_PRE_BLOCK_NAME && $this->b !== null)
            foreach ($this->b as $k => $v)
                if (strpos($v, ':') === false)
                    $this->b[$k] = 'minecraft:' . $v;
    }

    private function nonnullArr(& $in, $trimInner = true) {
        if (isset($in)) {
            $trim = trim($in);
            if ($trim !== "") {
                $csv = str_getcsv($trim);
                if ($trimInner) {
                    foreach ($csv as $k => $v)
                        $csv[$k] = trim($v);
                }
                return $csv;
            }
        }
        return null;
    }

    private function nonnullInt(& $in, $ifunset = null) {
        if (isset($in)) {
            $trim = trim($in);
            if ($trim !== "")
                return intval($trim);
        }
        return $ifunset;
    }

    public function getW() {
        return $this->w;
    }

    public function getU() {
        return $this->u;
    }

    public function getB() {
        return $this->b;
    }

    public function getE() {
        return $this->e;
    }

    public function prepareCheck() {
        $this->populate();
        $this->sqlParams = [];

        if (sizeof($this->sqlFromWhere) == 0)
            return "";

        $rets = [];

        if ($res = $this->generateCheckFromWhere(self::WORLD_MAP))
            $rets[self::WORLD_MAP] = $res;
        if ($res = $this->generateCheckFromWhere(self::USER_MAP))
            $rets[self::USER_MAP] = $res;
        if ($res = $this->generateCheckFromWhere(self::MATERIAL_MAP))
            $rets[self::MATERIAL_MAP] = $res;
        if ($res = $this->generateCheckFromWhere(self::ENTITY_MAP))
            $rets[self::ENTITY_MAP] = $res;

        $ret = "";
        foreach ($rets as $key => $val) {
            if ($ret) $ret .= " UNION ALL ";
            $ret .= $this->getSelect($key) . $val;
        }

        return $ret;
    }

    public function prepareStatementData() {
        $this->populate();
        $this->sqlParams = [];

        if (sizeof($this->sqlFromWhere) == 0)
            return "";

        if (sizeof($this->sqlFromWhere) == 1) {
            $v = reset($this->sqlFromWhere);
            $k = key($this->sqlFromWhere);
            $this->appenedSqlParams($this->fromWhereParamFilters[$k]);
            $this->appenedSqlParams(self::FILTER_LIMIT);
            return $this->getSelect($k) . " " . $v . " ORDER BY c.rowid " . $this->sqlOrder . " LIMIT ?, ?";
        }

        $queries = [];

        foreach ($this->sqlFromWhere as $table => $from) {
            $queries[$table] = $this->getSelect($table) . " " . $from;
        }


        $ret = "";
        foreach ($queries as $key => $val) {
            if ($ret) $ret .= " UNION ALL ";
            $ret .= "SELECT * FROM ($val ORDER BY c.rowid " . $this->sqlOrder . " LIMIT ?) AS t$key";
            $this->appenedSqlParams($this->fromWhereParamFilters[$key]);
            $this->appenedSqlParams(self::FILTER_LIMIT_SUM);
        }

        $this->appenedSqlParams(self::FILTER_LIMIT);
        return $ret . " ORDER BY time " . $this->sqlOrder . " LIMIT ?, ?";
    }

    public function prepareStatementCount() {
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

    public function getParams() {
        $this->populate();
        return $this->sqlParams;
    }

    private function appenedSqlParams($filter) {
        if (is_array($filter))
            foreach ($filter as $f)
                $this->sqlParams = array_merge($this->sqlParams, $this->whereParams[$f]);
        else
            $this->sqlParams = array_merge($this->sqlParams, $this->whereParams[$filter]);
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
                $dmVar = $this->useBlockdata ? 'IFNULL(dm.data, c.data)' : 'c.data';
                return "SELECT c.rowid, 'block' AS `table`, c.time, u.user, u.uuid, c.action, w.world, c.x, c.y, c.z, "
                    . (
                    $material && $entity
                        ? "CASE WHEN c.type=0 THEN um.user WHEN c.action=3 THEN em.entity ELSE mm.material END"
                        : ($material ? "mm.material" : "CASE WHEN c.type=0 THEN um.user ELSE em.entity END")
                    )
                    . " AS `target`, "
                    . (
                    $material && $entity
                        ? "CASE WHEN c.type=0 THEN um.uuid ELSE $dmVar END"
                        : ($material ? $dmVar : "CASE WHEN c.type=0 THEN um.uuid ELSE c.data END")
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
            case self::WORLD_MAP:
                return "SELECT 'world' AS `table`, world AS `name`, NULL AS uuid";
            case self::USER_MAP:
                return "SELECT 'user' AS `table`, user AS `name`, uuid";
            case self::MATERIAL_MAP:
                return "SELECT 'material' AS `table`, material AS `name`, NULL AS uuid";
            case self::ENTITY_MAP:
                return "SELECT 'entity' AS `table`, entity AS `name`, NULL AS uuid";
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
        $this->fromWhereParamFilters = [];

        if ($this->a & self::A_LOOKUP_TABLE == 0) {
            $this->whereParams = [];
            return;
        }

        $this->parseWheres();
        $this->whereParams[self::FILTER_LIMIT] = [$this->offset, $this->count];
        $this->whereParams[self::FILTER_LIMIT_SUM] = [$this->offset + $this->count];


        if ($this->a & self::A_BLOCK_TABLE) {
            /** @var string[] $wheres */
            $wheres = [self::FILTER_TIME, self::FILTER_USER, self::FILTER_WORLD, self::FILTER_COORDS, self::FILTER_ROLLBACK];
            /** @var string $sql */
            $sql = "FROM `" . $this->prefix . "block` AS c"
                . " LEFT JOIN `" . $this->prefix . "user` AS u ON c.user = u.rowid LEFT JOIN `" . $this->prefix . "world` AS w ON c.wid = w.rowid";

            if ($this->a & (self::A_BLOCK_MATERIAL)) {
                $sql .= " LEFT JOIN `" . $this->prefix . "material_map` AS mm ON c.action<>3 AND c.type=mm.rowid";
                if ($this->useBlockdata)
                    $sql .= " LEFT JOIN `" . $this->prefix . "blockdata_map` AS dm ON c.data<>0 AND c.action<>3 AND c.data=dm.rowid";
                $wheres[] = self::FILTER_MATERIAL;
            }
            if ($this->a & self::A_KILL) {
                $sql .= " LEFT JOIN `" . $this->prefix . "entity_map` AS em ON c.action=3 AND c.type<>0 AND c.type=em.rowid";
                $sql .= " LEFT JOIN `" . $this->prefix . "user` AS um ON c.data<>0 AND c.action=3 AND c.type=0 AND c.data=um.rowid";
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

            $this->sqlFromWhere[self::BLOCK] = $sql . $this->generateWhere(self::BLOCK, $wheres, $a);
        }

        if ($this->a & self::A_CONTAINER_TABLE) {
            /** @var string[] $wheres */
            $wheres = [self::FILTER_TIME, self::FILTER_USER, self::FILTER_WORLD, self::FILTER_COORDS, self::FILTER_ROLLBACK, self::FILTER_MATERIAL];
            /** @var string $sql */
            $sql = "FROM `" . $this->prefix . "container` AS c"
                . " LEFT JOIN `" . $this->prefix . "user` AS u ON c.user=u.rowid LEFT JOIN `" . $this->prefix . "world` AS w ON c.wid=w.rowid"
                . " LEFT JOIN `" . $this->prefix . "material_map` AS mm ON c.action<>3 AND c.type=mm.rowid";
            $a = null;
            if (($this->a & self::A_CONTAINER_TABLE) != self::A_CONTAINER_TABLE) {
                if ($this->a & self::A_CONTAINER_OUT)
                    $a = "c.action=0";
                if ($this->a & self::A_CONTAINER_IN)
                    $a = "c.action=1";
            }

            $this->sqlFromWhere[self::CONTAINER] = $sql . $this->generateWhere(self::CONTAINER, $wheres, $a);
        }

        if ($this->a & self::A_CHAT) {
            /** @var string[] $wheres */
            $wheres = [self::FILTER_TIME, self::FILTER_USER, self::W_KEYWORD_MESSAGE];
            /** @var string $sql */
            $sql = "FROM `" . $this->prefix . "chat` AS c"
                . " LEFT JOIN `" . $this->prefix . "user` AS u ON c.user=u.rowid";

            $this->sqlFromWhere[self::CHAT] = $sql . $this->generateWhere(self::CHAT, $wheres);
        }

        if ($this->a & self::A_COMMAND) {
            /** @var string[] $wheres */
            $wheres = [self::FILTER_TIME, self::FILTER_USER, self::W_KEYWORD_MESSAGE];
            /** @var string $sql */
            $sql = "FROM `" . $this->prefix . "command` AS c"
                . " LEFT JOIN `" . $this->prefix . "user` AS u ON c.user=u.rowid";

            $this->sqlFromWhere[self::COMMAND] = $sql . $this->generateWhere(self::COMMAND, $wheres);
        }

        if ($this->a & self::A_SESSION) {
            /** @var string[] $wheres */
            $wheres = [self::FILTER_TIME, self::FILTER_USER, self::FILTER_WORLD, self::FILTER_COORDS];
            /** @var string $sql */
            $sql = "FROM `" . $this->prefix . "session` AS c"
                . " LEFT JOIN `" . $this->prefix . "user` AS u ON c.user=u.rowid LEFT JOIN `" . $this->prefix . "world` AS w ON c.wid=w.rowid";

            $this->sqlFromWhere[self::SESSION] = $sql . $this->generateWhere(self::SESSION, $wheres);
        }

        if ($this->a & self::A_USERNAME) {
            /** @var string[] $wheres */
            $wheres = [self::FILTER_TIME, self::FILTER_USER, self::W_KEYWORD_USER];
            /** @var string $sql */
            $sql = "FROM `" . $this->prefix . "username_log` AS c"
                . " LEFT JOIN `" . $this->prefix . "user` AS u ON c.uuid=u.uuid";

            $this->sqlFromWhere[self::USERNAME] = $sql . $this->generateWhere(self::USERNAME, $wheres);
        }
    }

    /**
     * @param $table
     * @param string[] $columns
     * @param string $additional Additional where query
     * @return string pre-spaced at the beginning
     */
    private function generateWhere($table, $columns, $additional = null) {
        $this->fromWhereParamFilters[$table] = [];
        $wheres = $additional == null ? [] : [$additional];
        $me = 0;

        foreach ($columns as $filter) {
            if (isset($this->sqlWhereParts[$filter])) {
                if ($filter == self::FILTER_MATERIAL) {
                    $me |= 0b01;
                } elseif ($filter == self::FILTER_ENTITY) {
                    $me |= 0b10;
                } else {
                    $wheres[] = $this->sqlWhereParts[$filter];
                    $this->fromWhereParamFilters[$table][] = $filter;
                }
            }
        }

        if ($me == 0b11) {
            $wheres[] = "(" . $this->sqlWhereParts[self::FILTER_MATERIAL] . " OR " . $this->sqlWhereParts[self::FILTER_ENTITY] . ")";
            $this->fromWhereParamFilters[$table][] = self::FILTER_MATERIAL;
            $this->fromWhereParamFilters[$table][] = self::FILTER_ENTITY;
        } elseif ($me & 0b01) {
            $wheres[] = $this->sqlWhereParts[self::FILTER_MATERIAL];
            $this->fromWhereParamFilters[$table][] = self::FILTER_MATERIAL;
        } elseif ($me & 0b10) {
            $wheres[] = $this->sqlWhereParts[self::FILTER_ENTITY];
            $this->fromWhereParamFilters[$table][] = self::FILTER_ENTITY;
        }

        if (sizeof($wheres) == 0)
            return "";
        return " WHERE " . join(" AND ", $wheres);
    }

    private function parseWheres() {
        $this->sqlWhereParts = [];
        $this->whereParams = [];

        if (($this->a & self::A_WHERE_MATERIAL)) {
            if (empty($this->b)) {
                $this->sqlWhereParts[self::FILTER_MATERIAL] = 'c.action<>3';
                $this->whereParams[self::FILTER_MATERIAL] = [];
            } else {
                self::whereAbsoluteString(self::FILTER_MATERIAL, $this->b, $this->a & self::A_EX_BLOCK);
            }
        }
        if (($this->a & self::A_WHERE_ENTITY)) {
            if (empty($this->e)) {
                $this->sqlWhereParts[self::FILTER_ENTITY] = 'c.action=3';
                $this->whereParams[self::FILTER_ENTITY] = [];
            } else {
                self::whereAbsoluteString(self::FILTER_ENTITY, $this->e, $this->a & self::A_EX_ENTITY);
            }
        }
        if (($this->a & self::A_WHERE_COORDS) && !empty($this->w))
            self::whereAbsoluteString(self::FILTER_WORLD, $this->w, $this->a & self::A_EX_WORLD);
        if (!empty($this->u))
            self::whereAbsoluteString(self::FILTER_USER, $this->u, $this->a & self::A_EX_USER);
        if ($this->t !== null) {
            if ($this->a & self::A_REV_TIME) {
                $this->sqlWhereParts[self::FILTER_TIME] = self::W_TIME . '>= ?';
                $this->sqlOrder = 'ASC';
            } else {
                $this->sqlWhereParts[self::FILTER_TIME] = self::W_TIME . '<= ?';
                $this->sqlOrder = 'DESC';
            }
            $this->whereParams[self::FILTER_TIME] = [$this->t];
        } else {
            $this->sqlOrder = $this->a & self::A_REV_TIME ? 'ASC' : 'DESC';
        }

        if ($this->a & self::A_WHERE_COORDS && $this->x != null && $this->y != null && $this->z != null && $this->x2 != null && $this->y2 != null && $this->z2 != null) {
            $this->sqlWhereParts[self::FILTER_COORDS] = self::WHERE_XYZ;
            $this->whereParams[self::FILTER_COORDS] = [$this->x, $this->x2, $this->y, $this->y2, $this->z, $this->z2];
        }
        if ($this->a & self::A_WHERE_ROLLBACK && $this->a & (self::A_ROLLBACK_YES | self::A_ROLLBACK_NO)) {
            $this->sqlWhereParts[self::FILTER_ROLLBACK] = self::WHERE_ROLLED_BACK;
            $this->whereParams[self::FILTER_ROLLBACK] = [$this->a & self::A_ROLLBACK_YES ? 1 : 0];
        }

        if ($this->a & self::A_WHERE_KEYWORD && $this->keyword != null)
            $this->whereKeywordSearch();
    }

    /**
     * @param int $filter
     * @param string[] $query
     * @param boolean $exFlag
     */
    private function whereAbsoluteString($filter, $query, $exFlag) {
        $names = [];
        $uuids = [];

        if ($filter === self::FILTER_ENTITY || $filter === self::FILTER_USER) {
            foreach ($query as $k => $val) {

                if (strlen($val) == 36) { // TODO: Make sure $val is an actual UUID
                    $uuids[] = $val;
                } else {
                    $names[] = $val;
                }
            }
        } else {
            foreach ($query as $k => $val) {
                $names[] = $val;
            }
        }

        switch ($filter) {
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

        if (sizeof($names) || sizeof($uuids)) {
            if ($filter === self::FILTER_USER) {
                // Username and UUID
                // logic: if one is not set, the other is set.
                if (!sizeof($uuids)) {
                    $whereIn = $in . '(' . $this->qmPh($names) . ')';
                    $placeholders = $names;
                } elseif (!sizeof($names)) {
                    $whereIn = $inUuid . '(' . $this->qmPh($uuids) . ')';
                    $placeholders = $names;
                } else {
                    $whereIn = $in . '(' . $this->qmPh($names) . ') OR ' . $inUuid . '(' . $this->qmPh($uuids) . ')';
                    $placeholders = array_merge($names, $uuids);
                }
            } else {
                $whereIn = $in . '(' . $this->qmPh($names) . ')';
                $placeholders = $names;
            }

            $add = $this->selectIdWhere($tableId, $selectId, $whereIn, $exFlag);
        } else {
            $placeholders = [];
        }

        if ($filter === self::FILTER_ENTITY && sizeof($names)) {
            if (!sizeof($uuids)) {
                $whereIn2 = $inUser . '(' . $this->qmPh($names) . ')';
                $placeholders = array_merge($placeholders, $names);
            } elseif (sizeof($names)) {
                $whereIn2 = $inUuid . '(' . $this->qmPh($uuids) . ')';
                $placeholders = array_merge($placeholders, $uuids);
            } else {
                $whereIn2 = $inUser . '(' . $this->qmPh($names) . ') OR ' . $inUuid . '(' . $this->qmPh($uuids) . ')';
                $placeholders = array_merge($placeholders, $names, $uuids);
            }

            $add2 = $this->selectIdWhere($tableId2, $selectId2, $whereIn2, $exFlag);
            if (isset($add))
                $add = '(' . $add . ($exFlag ? ' AND ' : ' OR ') . $add2 . ')';
            else
                $add = $add2;
        }

        if ($filter === self::FILTER_ENTITY)
            $add = "(c.action=3 AND $add)";
        elseif ($filter === self::FILTER_MATERIAL)
            $add = "(c.action<>3 AND $add)";

        if (isset($add)) {
            $this->sqlWhereParts[$filter] = $add;
            $this->whereParams[$filter] = &$placeholders;
        }
    }

    private function selectIdWhere($tableId, $mapId, $whereIn, $exFlag) {
        return $tableId . ($exFlag ? ' NOT ' : ' ') . "IN (SELECT $mapId WHERE $whereIn)";
    }

    private function qmPh(& $array) {
        $len = sizeof($array);
        if ($len === 0)
            return '';
        $ret = '?';
        while (--$len)
            $ret .= ',?';
        return $ret;
    }

    private function whereKeywordSearch() {
        $placeholders = [];
        $msgParts = [];
        $usrParts = [];
        foreach ($this->keyword as $k => $val) {
            $msgParts[] = self::W_KEYWORD_MESSAGE . " LIKE ?";
            $usrParts[] = self::W_KEYWORD_USER . " LIKE ?";
            $placeholders = "%$val%";
        }
        $this->sqlWhereParts[self::FILTER_KEYWORD_MESSAGE] = sizeof($msgParts) == 1
            ? $msgParts[0] : "(" . join(" AND ", $msgParts) . ")";
        $this->sqlWhereParts[self::FILTER_KEYWORD_USER] = sizeof($msgParts) == 1
            ? $usrParts[0] : "(" . join(" AND ", $usrParts) . ")";
        $this->whereParams[self::FILTER_KEYWORD_MESSAGE] = &$placeholders;
        $this->whereParams[self::FILTER_KEYWORD_USER] = &$placeholders;
    }

    private function generateCheckFromWhere($map) {
        switch ($map) {
            case self::WORLD_MAP:
                if ($this->w === null)
                    return '';
                $table = 'world';
                $column = 'world';
                $list = $this->w;
                break;
            case self::USER_MAP:
                if ($this->u === null && $this->e === null)
                    return '';
                $table = 'user';
                $column = 'user';
                $list = $this->u;
                break;
            case self::MATERIAL_MAP:
                if ($this->b === null)
                    return '';
                $table = 'material_map';
                $column = 'material';
                $list = $this->b;
                break;
            case self::ENTITY_MAP:
                if ($this->e === null)
                    return '';
                $table = 'entity_map';
                $column = 'entity';
                $list = $this->e;
                break;
            default:
                return '';
        }

        if ($map === self::USER_MAP && $this->e !== null) {
            // Search entities as well
            if ($list === null)
                $list = $this->e;
            else
                $list = array_merge($list, $this->e);
        }

        $list = array_unique($list);

        if ($entity = ($map === self::ENTITY_MAP) || $map === self::USER_MAP) {
            // filter out UUIDs
            foreach ($list as $k => $v) {
                if (strlen($v) === 36) { // TODO: Make sure $val is an actual UUID
                    if ($entity)
                        $uuids[] = $v;
                    unset($list[$k]);
                }
            }
        }

        if (sizeof($list) === 0 && isset($uuids) && sizeof($uuids) === 0)
            return '';

        $params = isset($uuids) ? array_merge($list, $uuids) : $list;

        $this->sqlParams = array_merge($this->sqlParams, $params);
        return ' FROM ' . $this->prefix . $table . " WHERE $column IN(" . $this->qmPh($params) . ')';
    }
}