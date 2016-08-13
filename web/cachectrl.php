<?php
// CoLWI v0.9.0
// CacheCtrl PHP class
// Copyright (c) 2015-2016 SimonOrJ

// __construct ( string server, PDO codb, string co_, boolean legacySupport )
//   returns nothing.
// ... four more member functions.

// Class for things with cache.
// users, blocks, arts, images, entities,


// TODO: Make this update usernames somehow.
class CacheCtrl {
    private $ALL = ["art","entity","material","user","world"],
            $artDb = [],
            $entityDb = [],
            $materialDb = [],
            $userDb = [],
            $worldDb = [],
            $art, $artLookup,
            $entity, $entityLookup,
            $material, $materialLookup,
            $user, $userLookup,
            $world, $worldLookup,
            $fr, $codb, $co_, $legacy;
    
    public function __construct($server, $codb, $co_, $legacySupport) {
        // Set variables
        $this->fr = "cache/".$server."/"; // FileRoot
        $this->codb = $codb;
        $this->co_ = $co_;
        $this->legacy = $legacySupport;
        
        // Load 
        foreach($this->ALL as $d) if(file_exists($this->fr.$d.".php")) {
            $e = $d."Db";
            $this->$e = require($this->fr.$d.".php");
        }
    }
    
    public function __destruct() {
        if (!is_dir(__DIR__.'/'.$this->fr))
            mkdir(__DIR__.'/'.$this->fr);
        foreach($this->ALL as $v) {
            $e = $v."Lookup";
            if(!empty($this->$v) || !empty($this->$e)) {
                // Save $db to file
                $d = $v."Db";
                file_put_contents(__DIR__.'/'.$this->fr.$v.".php","<?php return ".var_export($this->$d,true).";?>");
            }
        }
    }
    
    public $error = [];
    
    // Function for id to value retrieval
    public function getValue($id,$from) {
        $lookup = $from."Lookup";
        $ac = $from."Db";
        $ac =& $this->$ac;
        if (array_key_exists($id,$ac)) $ret = $ac[$id];
        else {
            if(empty($this->$lookup)) $this->$lookup = $this->codb->prepare("SELECT `".$from."` FROM ".$this->co_.((in_array($from,["user","world"],true)) ? $from : $from."_map")." WHERE ".(($from == "user") ? "rowid" : "id")."=?;");
            $this->$lookup->execute([$id]);
            if($u = $this->$lookup->fetch(PDO::FETCH_NUM)) $ac[$id] = $u[0];
            else {
                $this->error[] = [$from,"id",$id];
                return NULL;
            }
            $ret = $u[0];
        }
        return (($from == "material") && ((strpos(":",$ret) !== false)) ? "minecraft:".$ret : $ret);
    }
    
    // Function for value to id retrieval
    public function getId($value,$from) {
        $ac = $from."Db";
        $ac =& $this->$ac;
        if ($id = array_search(strtolower($value),array_map("strtolower",$ac),true)) return $id;
        else {
            if(empty($this->$from)) $this->$from = $this->codb->prepare("SELECT `".(($from == "user") ? "rowid" : "id")."`, `".$from."` FROM ".$this->co_.((in_array($from,["user","world"],true)) ? $from : $from."_map")." WHERE ".$from."=?;");
            $this->$from->execute([$value]);
            if($i = $this->$from->fetch(PDO::FETCH_NUM)) $ac[$i[0]] = $i[1];
            else {
                $this->error[] = [$from,"value",$value];
                return NULL;
            }
            return $i[0];
        }
    }
}?>