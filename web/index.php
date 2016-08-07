<?php /* CoreProtect LWI by SimonOrJ. All Rights Reserved. */include "settings.php";$fm=($_GET["autoLookup"]?true:false);?><!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>CorePortect Lookup Web Interface &bull; by SimonOrJ</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap-glyphicons.css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.3/css/bootstrap.min.css" integrity="sha384-MIwDKRSSImVFAZCVLtU0LMDdON6KVCrZHyVQQj6e8wIEJkW4tvwqXrbMIya1vriY" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/css/bootstrap-datetimepicker.min.css">
  <link rel="stylesheet" href="res/css/main.css">
  <link rel="stylesheet" href="res/css/jquery-autocomplete.css">
</head>

<body data-spy="scroll" data-target="#row-pages">

<!-- Top navigation bar -->
<nav id="top" class="navbar navbar-light bg-faded navbar-full">
  <div class="container">
    <span class="navbar-brand">CoreProtect Lookup Web Interface</span>
    <ul class="nav navbar-nav">
	  <?php foreach($_index["navigation"] as $ll => $hf) echo '<li class="nav-item"><a class="nav-link" href="'.$hf.'">'.$ll.'</a></li>';?>
    </ul>
    <?php echo $_login["required"]?'<a href="./?action=clear_login" class="btn btn-outline-info pull-xs-right">logout</a>':"";?>
  </div>
</nav>
<nav id="scroll-nav" class="navbar navbar-dark bg-inverse navbar-fixed-bottom">
  <div class="container-fluid">
    <ul id="row-pages" class="nav navbar-nav">
      <li class="nav-item"><a class="nav-link" href="#top">Top</a></li>
    </ul>
  </div>
</nav>

<!-- Write alert box -->
<div class="container">
<?php // If it doesn't have write permission to the ./cache directory
if(!is_writable("./cache/")):?>
<div class="alert alert-warning alert-dismissible fade in" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><strong>Notice:</strong> The directory <code>./cache/</code> is not writable. Lookup may take marginally longer to process, and autocomplete will not have updated data. Please refer to readme.md for setup information.</div>
<?php endif;?>

<!-- Lookup Form -->
<div id="lookupForm" class="card">
<div class="card-header"><span class="h4 card-title">Make a Lookup</span></div>
<form id="lookup" class="card-block" role="form" method="get" action="./">
<div class="form-group row">
  <div class="col-lg-2 form-control-label">Actions</div>
  <div class="dtButtons btn-group col-lg-10">
    <label class="btn btn-secondary" for="abl" data-toggle="tooltip" data-placement="top" title="Block manipulation"><input type="checkbox" id="abl" name="a[]" value="block"<?php echo (!$fm||in_array("block",$a))?" checked":"";?>>Block</label>
    <label class="btn btn-secondary" for="acl" data-toggle="tooltip" data-placement="top" title="Clickable events (e.g. Chest, door, buttons)"><input type="checkbox" id="acl" name="a[]" value="click"<?php echo ($fm&&in_array("click",$a))?" checked":"";?>>Click</label>
    <label class="btn btn-secondary" for="acn" data-toggle="tooltip" data-placement="top" title="Item transaction from containers"><input type="checkbox" id="acn" name="a[]" value="container"<?php echo ($fm&&in_array("container",$a))?" checked":"";?>>Container</label>
    <label class="btn btn-secondary" for="ach"><input type="checkbox" id="ach" name="a[]" value="chat"<?php echo ($fm&&in_array("chat",$a))?" checked":"";?>>Chat</label>
    <label class="btn btn-secondary" for="acm"><input type="checkbox" id="acm" name="a[]" value="command"<?php echo ($fm&&in_array("command",$a))?" checked":"";?>>Command</label>
    <label class="btn btn-secondary" for="akl" data-toggle="tooltip" data-placement="top" title="Mob kills"><input type="checkbox" id="akl" name="a[]" value="kill"<?php echo ($fm&&in_array("kill",$a))?" checked":"";?>>Kill</label>
    <label class="btn btn-secondary" for="ass" data-toggle="tooltip" data-placement="top" title="Player login/logout event"><input type="checkbox" id="ass" name="a[]" value="session"<?php echo ($fm&&in_array("session",$a))?" checked":"";?>>Session</label>
    <label class="btn btn-secondary" for="aus" data-toggle="tooltip" data-placement="top" title="Username change history"><input type="checkbox" id="aus" name="a[]" value="username"<?php echo ($fm&&in_array("username",$a))?" checked":"";?>>Username</label>
  </div>
</div>
<div class="form-group row">
  <div class="col-lg-2 form-control-label">Toggle</div>
  <div class="col-lg-10">
    <button class="btn btn-secondary" type="button" id="rcToggle">Radius/Corners</button>
    <span class="dtButtons btn-group">
    <label class="btn btn-outline-success" for="rbt"><input type="radio" id="rbt" name="rollback" value="1"<?php echo ($fm&&$_GET["rollback"]==="1")?" checked":"";?>><span class="glyphicon glyphicon-ok"></span></label>
    <label class="btn btn-secondary active" for="rb"><input type="radio" id="rb" name="rollback" value=""<?php echo (!$fm||$_GET["rollback"]==="")?" checked":"";?>>Rollback</label>
    <label class="btn btn-outline-secondary" for="rbf"><input type="radio" id="rbf" name="rollback" value="0"<?php echo ($fm&&$_GET["rollback"]==="0")?" checked":"";?>><span class="glyphicon glyphicon-minus"></span></label>
    </span>
  </div>
</div>
<div class="form-group row">
    <label class="col-sm-2 form-control-label" for="x1" id="corner1">Center / Corner 1</label>
    <div class="col-lg-4 col-sm-10 groups-line" id="c1">
      <div class="input-group">
        <input class="form-control" type="number" id="x1" name="xyz[]" placeholder="x"<?php echo $fm?' value="'.$_GET["xyz"][0].'"':"";?>>
          <span class="input-group-btn" style="width:0"></span>
        <input class="form-control" type="number" id="y1" name="xyz[]" placeholder="y"<?php echo $fm?' value="'.$_GET["xyz"][1].'"':"";?>>
          <span class="input-group-btn" style="width:0"></span>
        <input class="form-control" type="number" id="z1" name="xyz[]" placeholder="z"<?php echo $fm?' value="'.$_GET["xyz"][2].'"':"";?>>
      </div>
    </div>
    <label class="col-sm-2 form-control-label" for="x2" id="corner2">Radius / Corner 2</label>
    <div class="col-lg-4 col-sm-10" id="c2">
      <div class="input-group">
        <input class="form-control" type="number" id="x2" name="xyz2[]" placeholder="Radius or x"<?php echo $fm?' value="'.$_GET["xyz2"][0].'"':"";?>>
        <span class="input-group-btn c2" style="width:0"></span>
        <input class="form-control c2" type="number" id="y2" name="xyz2[]" placeholder="y"<?php echo $fm?' value="'.$_GET["xyz2"][1].'"':"";?>>
        <span class="input-group-btn c2" style="width:0"></span>
        <input class="form-control c2" type="number" id="z2" name="xyz2[]" placeholder="z"<?php echo $fm?' value="'.$_GET["xyz2"][2].'"':"";?>>
      </div>
    </div>
</div>
<div class="form-group row">
  <label class="col-xs-2 form-control-label" for="wid">World</label>
  <div class="col-xs-10"><input class="form-control autocomplete" data-qftr="world" type="text" id="wid" name="wid" placeholder="world"<?php echo $fm?' value="'.$_GET["wid"].'"':"";?>></div>
</div>
<div class="form-group row">
  <label class="col-lg-2 form-control-label" for="usr">Users</label>
  <div class="input-group col-lg-10" >
    <span class="dtButtons input-group-btn"><label class="btn btn-secondary" for="eus"><input type="checkbox" id="eus" name="e[]" value="u"<?php echo ($fm&&in_array("u",$_GET["e"]))?" checked":"";?>>Exclude</label></span>
    <input class="form-control autocomplete" data-qftr="user" type="text" pattern="((#[a-zA-Z_]+)|([a-zA-Z0-9_]{2,16}))(,\s?((#[a-zA-Z_]+)|([a-zA-Z0-9_]{2,16})))*" id="usr" name="u" placeholder="Separate by single comma(,)"<?php echo $fm?' value="'.$_GET["u"].'"':"";?>>
  </div>
</div>
<div class="form-group row">
  <label class="col-lg-2 form-control-label" for="blk">Blocks</label>
  <div class="input-group col-lg-10">
    <span class="dtButtons input-group-btn"><label class="btn btn-secondary" for="ebl"><input type="checkbox" id="ebl" name="e[]" value="b"<?php echo ($fm&&in_array("b",$_GET["e"]))?" checked":"";?>>Exclude</label></span>
    <input class="form-control autocomplete" data-qftr="material" type="text" pattern="([^:]+:[^:,]+)+" id="blk" name="b" placeholder="minecraft:<block> - Separate by single comma(,)"<?php echo $fm?' value="'.$_GET["b"].'"':"";?>>
  </div>
</div>
<div class="form-group row">
  <label class="col-sm-2 form-control-label" for="kwd">Keyword</label>
  <div class="col-sm-10"><input class="form-control" type="text" id="kwd" name="keyword"<?php echo $fm?' value="'.$_GET["keyword"].'"':"";?> data-toggle="tooltip" data-placement="top" title='Space [&nbsp;] for AND. Comma [,] for OR. Enclose terms in quotes [""] to escape spaces/commas. Only applies to chat and command.'></div>
</div>
<div class="form-group row">
  <label class="col-sm-2 form-control-label" for="date">Date/Time</label>
  <div class="input-group col-lg-4 col-sm-10 groups-line">
    <span class="dtButtons input-group-btn">
      <label class="btn btn-secondary" for="trv"><input type="checkbox" id="trv" name="asendt"<?php echo ($fm&&$_GET["asendt"]=="on")?" checked":"";?>>Reverse</label>
    </span>
    <input class="form-control" type="datetime-local" id="date" name="t" placeholder="0000-00-00T00:00:00"<?php echo $fm?' value="'.$_GET["t"].'"':"";?>>
  </div>
  <input type="hidden" name="unixtime" value="on">
  <label class="col-sm-2 form-control-label" for="lim">Limit</label>
  <div class="col-lg-4 col-sm-10">
    <input class="form-control" type="number" id="lim" name="lim" min="1" placeholder="30"<?php echo $fm?' value="'.$_GET["lim"].'"':"";?>>
  </div>
</div>
<div class="row">
  <div class="col-sm-offset-2 col-sm-10">
    <input class="btn btn-secondary" type="submit" id="submitBtn" value="Make a Lookup">
  </div>
</div>
</form>
</div>
</div>

<!-- Output table -->
<div class="container-fluid">
<table id="output" class="table table-sm table-striped">
  <caption id="genTime"></caption>
  <thead class="thead-inverse">
  <tr id="row-0"><th>#</th><th>Time</th><th>User</th><th>Action</th><th>Coordinates / World</th><th>Block/Item:Data</th><th>Amount</th><th>Rollback</th></tr>
  </thead>
  <tbody id="mainTbl"><?php echo isset($mainTbl)?$mainTbl:'<tr><th scope="row">-</th><td colspan="7">Please submit a lookup.</td></tr>';?></tbody>
</table>
</div>

<!-- Load More form -->
<form class="container" id="loadMore" action="">
<div class="row">
  <div class="col-sm-offset-2 col-sm-8 form-group input-group">
    <label class="input-group-addon" for="moreLim">load next </label><input class="form-control" type="number" id="moreLim" name="lim" min="1" placeholder="30">
  </div>
</div>
<input type="hidden" id="SQL" name="SQL">
<input type="hidden" id="SQLqs" name="SQLqs">
<input type="hidden" id="offset" name="offset">
<div class="form-group row">
  <div class="col-sm-offset-2 col-sm-8">
    <input class="btn btn-secondary" id="loadMoreBtn" type="submit" value="Load more">
  </div>
</div>
</form>


<div class="container">
  <div class="btn-group" role="group">
    <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
      Advanced
    </button>
    <div class="dropdown-menu" aria-labelledby="Advanced">
      <button id="purgeCache" class="dropdown-item list-group-item-danger">Purge cache</button>
    </div>
  </div><p>If you encounter any issues, please open an issue or a ticket on the <a href="https://github.com/SimonOrJ/CoreProtect-Lookup-Web-Interface">GitHub project page</a>.<!-- or the <a href="http://dev.bukkit.org/bukkit-plugins/coreprotect-lwi/">Bukkit plugin project page</a>.--><br>This webserver is running PHP <?php echo phpversion();?>.</p>
</div>

<!-- Copyright Message -->
<p>&copy; <?php echo $_index["copyright"];?> &mdash; CoreProtect LWI version 0.8.2-beta<br>Created by <a href="http://simonorj.com/">SimonOrJ</a>.</p>

<!-- All the scripting needs -->
<script>
// Quick Styling for JS-enabled browser
document.getElementById("corner1").innerHTML = "Center";
document.getElementById("corner2").innerHTML = "Radius";
document.getElementById("c2").className = "col-lg-4 col-sm-10";
a = document.getElementsByClassName("c2");
for(var i = 0; i < a.length; i++) a[i].style.display = "none";
a = document.getElementsByClassName("dtButtons");
for(var i = 0; i < a.length; i++) a[i].setAttribute("data-toggle","buttons");
document.getElementById("x2").setAttribute("placeholder","Radius");
document.getElementById("date").setAttribute("type","text");
document.getElementById("date").removeAttribute("name");
document.getElementById("loadMoreBtn").setAttribute("disabled","");
// Get variables from the settings
$dateFormat = "<?php echo $_index["dateFormat"];?>";
$timeFormat = "<?php echo $_index["timeFormat"];?>";
$timeDividor = <?php echo $_index["timeDividor"];?>*1000;
$dynmapURL = "<?php echo $_index["dynmapURL"];?>";
$dynmapZoom = "<?php echo $_index["dynmapZoom"];?>";
$dynmapMapName = "<?php echo $_index["dynmapMapName"];?>";
$pageInterval = <?php echo $_index["pageInteval"];?>;
$fm = <?php echo $fm?"true":"false";?>;
$PHP_$t = <?php echo ($fm&&$_GET["t"]!=="")?' value="'.$_GET["t"].'"':"false";?>;
</script>
<script src="//ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js">// JQuery</script>
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js">// Dropdown</script>
<script src="//cdnjs.cloudflare.com/ajax/libs/tether/1.1.1/js/tether.min.js">// Bootstrap dependency</script>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.2/js/bootstrap.min.js" integrity="sha384-vZ2WRJMwsjRMW/8U7i6PWi6AlO1L79snBrmgiDpgIWJ82z8eA5lenwvxbMV1PAh7" crossorigin="anonymous">// Bootstrap (Alpha!)</script>
<script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.1/moment.min.js">// datetime-picker dependency</script>
<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js">// Datetime Picker</script>
<script src="res/js/out-table.js"></script>
<script src="res/js/form-handler.js"></script>
</body>
</html>
