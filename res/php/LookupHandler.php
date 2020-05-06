<?php
/**
 * Class LookupHandler
 * TODO: decommission this file?
 */
class LookupHandler {
    /** @var integer[] */
    private $a, $xyz;
    /** @var string[] */
    private $b, $e, $u;
    /** @var integer */
    private $r, $t;
    /** @var boolean */
    private $eu, $treverse;

    /**
     * LookupHandler constructor.
     * @param array $_REQUEST
     */
    public function __construct(array $_REQUEST) {
        $this->a = $_REQUEST["a"];
        $this->b = $_REQUEST["b"];
        $this->e = $_REQUEST["e"];
        $this->eu = $_REQUEST["eu"];
        $this->r = $_REQUEST["r"];
        $this->xyz = $_REQUEST["xyz"];
        $this->t = $_REQUEST["t"];
        $this->treverse = $_REQUEST["treverse"];
        $this->u = $_REQUEST["u"];
    }

    /**
     * @param integer $unix
     * @param boolean $reverse
     * @return string SQL time statement
     */
    private function time($unix, $reverse) {
        if ($reverse)
            return "time>=".$unix;
        else
            return "time<=".$unix;
    }


}
