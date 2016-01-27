<?php include "settings.php"?><!DOCTYPE html>
<!--
// Developed by SimonOrJ.
// Alpha stage
-->
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <title>CorePortect Lookup Web Interface &bull; by SimonOrJ</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap-glyphicons.css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.2/css/bootstrap.min.css" integrity="sha384-y3tfxAZXuh4HwSYylfB+J125MxIs6mR5FOHamPBG064zB+AFeWH94NdvaCBm8qnd" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/css/bootstrap-datetimepicker.min.css">
  <link rel="stylesheet" href="res/bootstrap-v4.0.0-alpha.2-overstyles.css">
</head>
<body>
<div class="container">
<h1>CoreProtect Lookup Web Interface</h1>
<p class="second">by SimonOrJ</p>
<p>This project is still undergoing alpha testing.  Please report any problems or feedback to the <a href="https://github.com/SimonOrJ/CoreProtect-Lookup-Web-Interface">GitHub project page</a>.  Thank you for testing! ~SimonOrJ</p>
<div id="test"></div>
<div id="debug"></div>

<!--
- time(t) in seconds and t2
- block(b)
- chat/command/sign search (keyword)
- exclude (e) players or blocks.

a inputs:
'block','chat','click','command','container','kill','session','username'
-->
<div id="lookupForm" class="card">
<div class="card-header"><span class="h4 card-title">Make a Lookup</span></div>
<form id="lookup" class="card-block" role="form" method="get" action="./">
<div class="form-group row">
  <div class="col-lg-2 form-control-label">Actions</div>
  <div class="dtButtons btn-group col-lg-10">
    <label class="btn btn-secondary" for="abl"><input type="checkbox" id="abl" name="a[]" value="block" checked>Block</label>
    <label class="btn btn-secondary" for="acl"><input type="checkbox" id="acl" name="a[]" value="click">Click</label>
    <label class="btn btn-secondary" for="acn"><input type="checkbox" id="acn" name="a[]" value="container">Container</label>
    <label class="btn btn-secondary" for="ach"><input type="checkbox" id="ach" name="a[]" value="chat">Chat</label>
    <label class="btn btn-secondary" for="acm"><input type="checkbox" id="acm" name="a[]" value="command">Command</label>
    <label class="btn btn-secondary" for="aki"><input type="checkbox" id="akl" name="a[]" value="kill">Kill</label>
    <label class="btn btn-secondary" for="ass"><input type="checkbox" id="ass" name="a[]" value="session">Session</label>
    <label class="btn btn-secondary" for="aus"><input type="checkbox" id="aus" name="a[]" value="username">Username</label>
  </div>
</div>
<div class="form-group row">
  <div class="col-lg-2 form-control-label">Toggle</div>
  <div class="col-lg-10">
    <button class="btn btn-secondary" type="button" id="rcToggle" onClick="radius()">Radius/Corners</button>
    <span class="dtButtons btn-group">
    <label class="btn btn-success-outline" for="rbt"><input type="radio" id="rbt" name="rollback" value="1"><span class="glyphicon glyphicon-ok"></span></label>
    <label class="btn btn-secondary active" for="rb"><input type="radio" id="rb" name="rollback" value="" checked>Rollback</label>
    <label class="btn btn-secondary-outline" for="rbf"><input type="radio" id="rbf" name="rollback" value="0"><span class="glyphicon glyphicon-minus"></span></label>
    </span>
  </div>
</div>
<div class="form-group row">
    <label class="col-sm-2 form-control-label" for="x1" id="corner1">Center / Corner 1</label>
    <div class="input-group col-lg-4 col-sm-10 groups-line" id="c1">
      <input class="form-control" type="number" id="x1" name="xyz[]" placeholder="x">
        <span class="input-group-btn" style="width:0"></span>
      <input class="form-control" type="number" id="y1" name="xyz[]" placeholder="y">
        <span class="input-group-btn" style="width:0"></span>
      <input class="form-control" type="number" id="z1" name="xyz[]" placeholder="z">
    </div>
    <label class="col-sm-2 form-control-label" for="x2" id="corner2">Radius / Corner 2</label>
    <div class="input-group col-lg-4 col-sm-10" id="c2">
      <input class="form-control" type="number" id="x2" name="xyz2[]" placeholder="Radius or x">
      <span class="input-group-btn c2" style="width:0"></span>
      <input class="form-control c2" type="number" id="y2" name="xyz2[]" placeholder="y">
      <span class="input-group-btn c2" style="width:0"></span>
      <input class="form-control c2" type="number" id="z2" name="xyz2[]" placeholder="z">
    </div>
</div>
<div class="form-group row">
  <label class="col-xs-2 form-control-label" for="wid">World</label>
  <div class="col-xs-10"><input class="form-control" type="text" id="wid" name="wid" placeholder="world"></div>
</div>
<div class="form-group row">
  <label class="col-lg-2 form-control-label" for="usr">Users</label>
  <div class="input-group col-lg-10" >
    <span class="dtButtons input-group-btn"><label class="btn btn-secondary" for="eus"><input type="checkbox" id="eus" name="e[]" value="u">Exclude</label></span>
    <input class="form-control" type="text" pattern="((#[a-zA-Z_]+)|([a-zA-Z0-9_]{2,16}))(,\s?((#[a-zA-Z_]+)|([a-zA-Z0-9_]{2,16})))*" id="usr" name="u" placeholder="Separate by single comma(,)">
  </div>
</div>
<div class="form-group row">
  <label class="col-lg-2 form-control-label" for="blk">Blocks</label>
  <div class="input-group col-lg-10">
    <span class="dtButtons input-group-btn"><label class="btn btn-secondary" for="ebl"><input type="checkbox" id="ebl" name="e[]" value="b">Exclude</label></span>
    <input class="form-control" type="text" pattern="([^:]+:[^:,]+)+" id="blk" name="b" placeholder="minecraft:<block> - Separate by single comma(,)">
  </div>
</div>
<div class="form-group row">
  <label class="col-sm-2 form-control-label" for="kwd">Keyword</label>
  <div class="col-sm-10"><input class="form-control" type="text" id="kwd" name="keyword" placeholder="Coming in v0.6.x-alpha!" disabled></div>
</div>
<div class="form-group row">
  <label class="col-sm-2 form-control-label" for="date">Date/Time</label>
  <div class="input-group col-lg-4 col-sm-10 groups-line">
    <span class="dtButtons input-group-btn">
      <label class="btn btn-secondary" for="trv"><input type="checkbox" id="trv" name="asendt">Reverse</label>
    </span>
    <input class="form-control" type="datetime-local" id="date" name="t" placeholder="--/--/---- --:-- --">
  </div>
  <input type="hidden" name="unixtime" value="on">
  <label class="col-sm-2 form-control-label" for="lim">Limit</label>
  <div class="col-lg-4 col-sm-10">
    <input class="form-control" type="number" id="lim" name="lim" min="1" placeholder="30">
  </div>
</div>
<div class="row">
  <div class="col-sm-offset-2 col-sm-10">
    <input class="btn btn-secondary" type="submit" value="Submit">
    <input class="btn btn-secondary" type="reset" value="Reset">
  </div>
</div>
</form>
</div>
</div>
<div class="container-fluid">
<table id="output" class="table table-sm table-striped">
  <caption id="genTime"></caption>
  <thead class="thead-inverse">
  <tr><th>Time</th><th>User</th><th>Action</th><th>Coordinates / World</th><th>Block/Item:Data</th><th>Amount</th><th>Rollback</th></tr>
  </thead>
  <tbody id="mainTbl"><tr><td colspan="7">Please submit a lookup.</td></tr></tbody>
</table>
</div>
<form class="container" id="loadMore" action="">
<div class="row">
  <div class="col-sm-offset-2 col-sm-8 form-group input-group">
    <label class="input-group-addon" for="moreLim">load next </label><input class="form-control" type="number" id="moreLim" name="lim" min="1" placeholder="10">
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
<script>
// Quick Styles for JS-enabled browser
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
</script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.1.1/js/tether.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.2/js/bootstrap.min.js" integrity="sha384-vZ2WRJMwsjRMW/8U7i6PWi6AlO1L79snBrmgiDpgIWJ82z8eA5lenwvxbMV1PAh7" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.1/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js"></script>
<script>
// TODO: Transition this to jquery
getId = function(val) {return document.getElementById(val)},
getForm = function(val) {return document.lookup[val]};
$("#date").datetimepicker({format:"<?=$dateFormat.' '.$timeFormat?>"});
$("[for=abl]").addClass("active");

/*
 * Styling
 */

// Radius/Corners toggle
function radius() {
    if($("#corner1").text() == "Corner 1") {
        $("#corner1").text("Center");
        $("#corner2").text("Radius");
        $("#c2").removeClass("input-group");
        $(".c2").val("");
        $(".c2").hide();
        $("#x2").attr("placeholder","Radius");
    }
    else {
        $("#corner1").text("Corner 1");
        $("#corner2").text("Corner 2");
        $("#c2").addClass("input-group");
        $(".c2").show();
        $("#x2").attr("placeholder","x");
    }
}

// Main Submit
$("#lookup").submit(function($thislookup) {
    $thislookup.preventDefault();
    $.ajax("conn.php",{
        beforeSend:function(xhr,s){if($("#date").val()!=="")req=s.data+="&t="+moment($("#date").val(),"<?=$dateFormat.' '.$timeFormat?>").format("X");unixNow = Date.now()/1000|0;},
        data:$("#lookup").serialize(),
        dataType:"json",
        method:"POST",
        complete:function(){},
        success:function(data){reachedLimit(false);$lastDataTime = Date.now();phraseReturn(data)},
    })
});

// More Submit
$("#loadMore").submit(function($thislookup) {
    $thislookup.preventDefault();
    $.ajax("conn.php",{
        data:$("#loadMore").serialize(),
        dataType:"json",
        method:"POST",
        complete:function(){},
        success:function(data){phraseReturn(data,1)},
    })
});

function reachedLimit(toggle) {
  $("#loadMoreBtn").prop("disabled",toggle);
  if(toggle) {
      return '<i>No more results</i>'
  }
}

// Select value from table into query
function putInQ(element,type,flag) {
    switch(type) {
        case "time":
            $("#date").val(moment(parseInt($(element).parent().attr("data-time"))).format("<?=$dateFormat.' '.$timeFormat?>"));
            if(flag=="desc") {
                $("[for=trv]").removeClass("active");
                $("#trv").prop("checked",false);
            }
            else {
                $("[for=trv]").addClass("active");
                $("#trv").prop("checked",true);
            }
            break;
    }
    return false;
}

<?php
// TODO: Add "top" button that follows as you scroll and put this code:
//$("html, body").animate({ scrollTop: $('#title1').offset().top }, 1000);
?>

// Simple exist function
function if_exist(value,if_not) {
    if(value==="") return if_not;
    else return value;
}


/* class data:
  rDrop
  t
  u
  xyz
  b
*/

// Dropdown menu creation function
$("#output").on("show.bs.dropdown",".rDrop",function(){
    if(!$(this).hasClass("dropdown")) {
        $(this).addClass("dropdown");
        ($(this).hasClass("t"))?$(this).append('<div class="dropdown-menu"><span class="dropdown-header">Date/Time</span><span class="dropdown-item cPointer tAsc">Search ascending</span><span class="dropdown-item cPointer tDesc">Search descending</span></div>')
        :($(this).hasClass("u"))?$(this).append('<div class="dropdown-menu"><span class="dropdown-header">User</span><span class="dropdown-item cPointer uSch">Search block</span><span class="dropdown-item cPointer uESch">Exclusive Search</span></div>')
        :($(this).hasClass("c"))?$(this).append('<div class="dropdown-menu"><span class="dropdown-header">Coordinates</span><span class="dropdown-item cPointer cFl1">Center/Corner 1</span><span class="dropdown-item cPointer cFl2">Corner 2</span></div>')
        :($(this).hasClass("b"))?$(this).append('<div class="dropdown-menu"><span class="dropdown-header">Block</span><span class="dropdown-item cPointer bSch">Search block</span><span class="dropdown-item cPointer bESch">Exclusive Search</span></div>')
        :$(this).append('<div class="dropdown-menu"><span class="dropdown-header">wat</span></div>');
    }
});
// Displaying sign data function
$("#output").on("click.collapse-next.data-api",".collapse-toggle",function(){$(this).next().collapse("toggle")});

// returns data in table format
spanSign = '<span class="collapse-toggle" data-toggle="collapse-next" aria-expanded="false">'
divSignData = function(Lines) {return '<div class="mcSign">'+Lines[0]+'<br>'+Lines[1]+'<br>'+Lines[2]+'<br>'+Lines[3]+"</div>";}
spanDToggle =  '<span class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
function phraseReturn(obj,more) {
    $("#genTime").text("Request generated in "+Math.round(obj[0]["duration"]*1000)+"ms");
    if (obj[0]["status"]) { // If failed
        o = '<tr><td colspan="7"';
        switch(obj[0]["status"]) {
            case 1:
                o += ' class="text-xs-center">'+reachedLimit(true);
            break;
            case 2:
                o += "><b>The request did not go through properly.</b></td></tr><tr><td>"+obj[1][0]+"</td><td>"+obj[1][1]+'</td><td colspan="7">Error '+obj[1][2];
                reachedLimit(true);
            break;
            case 3:
                o += '><b>The webserver could not establish a connection to the database.</b> Please check your settings.</td></tr><tr><td colspan="7">PDO Exception: '+obj[1];
                
            break;
            case 4:
                o += "><b>The following value does not exist in the CoreProtect's database:</b></td></tr>"
                for(var i=0; i<obj[1].length;i++) {
                    o += "<tr><td></td><td>";
                    switch(obj[1][i][0]) {
                        // [material,id or value, thing that has weird stuff]
                        case "material":
                            o += 'Block</td><td colspan="5">'+obj[1][i][2];
                            break;
                        case "user":
                            o += 'Username</td><td colspan="5">'+obj[1][i][2];
                            break;
                        default:
                            o += e[i][0]+'</td><td colspan="5">'+obj[1][i][2];
                    }
                    o += "</td></tr>"
                }
                reachedLimit(true);
                break;
            default:
                o += "><b>Unexpected Error "+obj[0]["status"]+":</b> "+obj[0]["reason"];
                break;
        }
        o += '</td></tr>';
    }
    else { // Success
        if(more) $("#offset").val(parseInt($("#offset").val())+parseInt(if_exist($("#moreLim").val(),10)));
        else { // Set form values for offset lookup
            $("#SQL").val(obj[0]["SQL"]);
            $("#SQLqs").val(obj[0]["SQLqs"]);
            $("#offset").val(parseInt(if_exist($("#lim").val(),30)));
        }
        var r = obj[1];
        o = "";
        var i;
        for (i = 0; i<r.length; i++) {
            // UNIX to JS Date
            r[i]["time"] *= 1000;
            if(<?=$timeDividor?> < Math.abs($lastDataTime-r[i]["time"])||!moment($lastDataTime).isSame(r[i]["time"],"day")) o += '<tr class="table-section"><th colspan="7">'+moment(r[i]["time"]).calendar(null,{
                sameDay: '[Today at] <?=$timeFormat?>',
                nextDay: '[Please Configure Correct Minecraft Server Time]',
                nextWeek: '[Please Configure Correct Minecraft Server Time]',
                lastDay: '[Yesterday at] <?=$timeFormat?>',
                lastWeek: '[Last] dddd, <?=$dateFormat?>',
                sameElse: "<?=$dateFormat?>"
            })+"</th></tr>";
            o += "<tr";
            if (r[i]['rolled_back'] == '1') o += ' class="table-success"';

            // Time, Username, Action
            o += '><td class="rDrop t" title="'+moment(r[i]["time"]).format("<?=$dateFormat?>")+'">'+spanDToggle+moment(r[i]["time"]).format("<?=$timeFormat?>")+'</span></td><td class="rDrop u">'+spanDToggle+r[i]['user']+'</span></td><td>'+r[i]['table']+'</td><td';
            $lastDataTime = r[i]["time"];
            switch(r[i]["table"]) {
                case "click":
                case "session":
                    r[i]['rolled_back'] = "";
                case "container":
                case "block":
                case "kill":
                    // rolled_back translation
                    if(r[i]['rolled_back']) {
                        if(r[i]['rolled_back'] == "0") r[i]['rolled_back'] = "Not rolled.";
                        else if(r[i]['rolled_back'] == "1") r[i]['rolled_back'] = "Rolled.";
                    }
                    // Coordinates, Type:Data, Amount, Rollback
                    o += ' class="rDrop c">'+spanDToggle+r[i]['x']+' '+r[i]['y']+' '+r[i]['z']+' '+r[i]['wid']+"</span></td><td"+((r[i]["table"] == "session")?">"
                    :((r[i]["signdata"])?' class="rColl">'+spanSign
                    :' class="rDrop b">'+spanDToggle)+r[i]['type']+':'+r[i]['data']+"</span>"+((r[i]["signdata"])? '<div class="rDrop b collapse">'+divSignData(r[i]["signdata"])+"<br>"+spanDToggle+r[i]['type']+':'+r[i]['data']+"</span></div>"
                    :""))+'</td><td'+((r[i]['action'] == "0")?' class="table-warning">-'
                    :((r[i]['action'] == "1")?' class="table-info">+'
                    :'>'))+((r[i]['table'] == "container") ? r[i]['amount'] 
                    : '')+'</td><td>'+r[i]['rolled_back'];
                    break;
                case "chat":
                case "command":
                case "username_log":
                    // Message/UUID
                    o += ' colspan="4">'+r[i]['data']
            }
            o +='</td></tr>';
        }
    }
    if(more) $("#mainTbl").append(o);
    else $("#mainTbl").html(o);
}
</script>
<div class="container">
<p>Index last updated  Jan 25, 2016.  Version 0.6.0-alpha</p>
<p>This web app utilizes <a href="http://v4-alpha.getbootstrap.com/">Bootstrap v4</a> and <a href="https://eonasdan.github.io/bootstrap-datetimepicker/"> Bootstrap Datepicker v4</a>.<br>COLWI &copy; SimonOrJ, 2015-<?=date("Y")?>.  All Rights Reserved.</p>
</div>
</body>
</html>
