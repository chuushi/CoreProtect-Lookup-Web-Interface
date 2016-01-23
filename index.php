<!DOCTYPE html>
<!--
// Developed by SimonOrJ.  Please do not modify.
// Alpha stage
-->
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <title>CorePortect Web Lookup Interface &bull; by SimonOrJ</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap-glyphicons.css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.2/css/bootstrap.min.css" integrity="sha384-y3tfxAZXuh4HwSYylfB+J125MxIs6mR5FOHamPBG064zB+AFeWH94NdvaCBm8qnd" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/css/bootstrap-datetimepicker.min.css">
  <style> /* Default Style Overrides */
.btn {
    margin-bottom:0;
}
.bg-warning, .bg-info {
  color:#333 !important;
}
.bg-warning {
  background-color:#fcf8e3 !important;
}
.table-striped tbody tr:nth-of-type(odd) .bg-warning {
  background-color:#fbf6dd !important;
}
.bg-info {
  background-color:#d9edf7 !important;
}
.table-striped tbody tr:nth-of-type(odd) .bg-info {
  background-color:#d2e9f5 !important;
}
  </style>
</head>
<body>
<h1>CoreProtect Web Lookup Interface</h1>
<p class="second">by SimonOrJ</p>
<p>This project is still undergoing alpha testing.  Please report any problems or feedback to the <a href="https://github.com/SimonOrJ/CoreProtect-Lookup-Web-Interface">GitHub project page</a>.  Thank you for testing! ~SimonOrJ</p>
<div id="test"></div>
<div id="debug"></div>
<div class="container">
<form name="lookup" id="lookup" action="javascript:getInformation();">

<!--
- time(t) in seconds and t2
- block(b)
- chat/command/sign search (keyword)
- exclude (e) players or blocks.

a inputs:
'block','chat','click','command','container','kill','session','username'
-->
<div class="form-group row">
  <div class="col-lg-2 form-control-label">Actions</div>
  <div class="btn-group col-lg-10" data-toggle="buttons">
    <label class="btn btn-secondary active" for="abl"><input type="checkbox" id="abl" name="a[]" value="block" checked>Block</label>
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
    <button class="btn btn-secondary" type="button" onClick="radius()">Radius/Corner</button>
    <span class="btn-group" data-toggle="buttons">
    <label class="btn btn-info-outline" for="rbt"><input type="radio" id="rbt" name="rb" value="1"><span class="glyphicon glyphicon-ok"></span></label>
    <label class="btn btn-secondary active" for="rb"><input type="radio" id="rb" name="rb" value="" checked>Rollback</label>
    <label class="btn btn-secondary-outline" for="rbf"><input type="radio" id="rbf" name="rb" value="0"><span class="glyphicon glyphicon-minus"></span></label>
    </span>
  </div>
</div>
<div class="form-group row">
  <label class="col-md-2 form-control-label" for="x1" id="corner1">Center / Corner 1</label>
  <div class="input-group col-md-10">
    <input class="form-control" type="number" id="x1" name="xyz[]" placeholder="x">
      <span class="input-group-btn" style="width:0"></span>
    <input class="form-control" type="number" id="y1" name="xyz[]" placeholder="y">
      <span class="input-group-btn" style="width:0"></span>
    <input class="form-control" type="number" id="z1" name="xyz[]" placeholder="z">
  </div>
</div>
<div class="form-group row">
  <label class="col-md-2 form-control-label" for="x2" id="corner2">Radius / Corner 2</label>
  <div class="input-group col-md-10" id="c2">
    <input class="form-control" type="number" id="x2" name="xyz2[]" placeholder="Radius or x">
    <span class="input-group-btn c2" style="width:0"></span>
    <input class="form-control c2" type="number" id="y2" name="xyz2[]" placeholder="y">
    <span class="input-group-btn c2" style="width:0"></span>
    <input class="form-control c2" type="number" id="z2" name="xyz2[]" placeholder="z">
  </div>
</div>
<div class="form-group row">
  <label class="col-lg-2 form-control-label" for="usr">Users</label>
  <div class="input-group col-lg-10" >
    <span class="input-group-btn" data-toggle="buttons"><label class="btn btn-secondary" for="eus"><input type="checkbox" id="eus" name="e[]" value="u">Exclude</label></span>
    <input class="form-control" type="text" pattern="((#[a-zA-Z_]+)|([a-zA-Z0-9_]{2,16}))(,\s?((#[a-zA-Z_]+)|([a-zA-Z0-9_]{2,16})))*" id="usr" name="u" placeholder="Separate by single comma(,)">
  </div>
</div>
<div class="form-group row">
  <label class="col-lg-2 form-control-label" for="blk">Blocks</label>
  <div class="input-group col-lg-10">
    <span class="input-group-btn" data-toggle="buttons"><label class="btn btn-secondary" for="ebl"><input type="checkbox" id="ebl" name="e[]" value="b">Exclude</label></span>
    <input class="form-control" type="text" pattern="([^:]+:[^:,]+)+" id="blk" name="b" placeholder="minecraft:<block> - Separate by single comma(,)">
  </div>
</div>
<div class="form-group row">
  <label class="col-sm-2 form-control-label" for="kwd">Keyword</label>
  <div class="col-sm-10"><input class="form-control" type="text" id="kwd" name="keyword" placeholder="Coming in v0.6.x-alpha!" disabled></div>
</div>
<div class="form-group row">
  <label class="col-sm-2 form-control-label" for="date">Date From</label>
  <div class="input-group col-sm-10">
    <span class="input-group-btn" data-toggle="buttons"><label class="btn btn-secondary" for="trv"><input type="checkbox" id="trv" name="asendt">Reverse</label></span>
    <input class="form-control" type="datetime-local" id="date" name="date" placeholder="--/--/---- --:-- --">
  </div>
</div>
<div class="form-group row">
  <label class="col-sm-2 form-control-label" for="lim">Query Limit</label>
  <div class="col-sm-10"><input class="form-control" type="number" id="lim" name="lim" placeholder="30"></div>
</div>
<div class="form-group row">
  <div class="col-sm-offset-2 col-sm-10">
    <input class="btn btn-secondary" type="submit" value="Submit">
    <input class="btn btn-secondary" type="reset" value="Reset">
  </div>
</div>
</form>
</div>
<table id="output" class="table table-bordered table-striped">
  <thead>
  <tr><th>Time ago</th><th>User</th><th>Action</th><th>Coordinates / World</th><th>Block/Item:Data</th><th>Amount</th><th>Rollback</th></tr>
  </thead>
  <tbody id="maintbl"><tr><td colspan="9">Please submit a lookup.</td></tr></tbody>
</table>
<form name="getmore" action="javascript:getMoreInformation();">
<label for="more">load next </label><input type="number" id="more" name="more" placeholder="10"><br>
<input type="submit" value="Load more">
</form>

<script>
// Quick Styles
document.getElementById("corner1").innerHTML = "Center";
document.getElementById("corner2").innerHTML = "Radius";
document.getElementById("c2").className = "col-md-10";
c2 = document.getElementsByClassName("c2");
for(var i = 0; i < c2.length; i++) c2[i].style.display = "none";
document.getElementById("x2").setAttribute("placeholder","Radius");
document.getElementById("date").setAttribute("type","text");
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

/*
 * Styling
 */
function ready() {
    $("#date").datetimepicker();
    getId("lookup").className = "json";
}

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

function checkClick(x) {
    if (x.getElementsByTagName('input')[0].checked) x.classList.add('checked');
    else x.classList.remove('checked');
}


/*
 * Table Generation
 */

// when submitted
function compile() {
//    var a,b,t,e,r,u,lim,xyz,xyz2;
    var rChecked = function(val,setv,check) { // This function has a bad name...
            var ls = [],res;
            for (var i=0; i < getForm(val).length; i++) if(getForm(val)[i].checked || !check) ls.push(getForm(val)[i].value);
            res = ls.join();
            return "&"+setv+'='+encodeURIComponent(res);
    }
    
    req = rChecked("a[]",'a',1);
    if (getId('x1').value && getId('y1').value && getId('z1').value) {
        req += rChecked("xyz[]",'xyz');
        if (getId('x2').value && getId('y2').value && getId('z2').value) req += rChecked("xyz2[]",'xyz2');
        else if (st = getId('x2').value) req += "&r="+encodeURIComponent(st);
        else req += "&r=0"
    }
    if (st = getId('usr').value) req += "&u="+encodeURIComponent(st.replace(/\s/g, ''));
    if (st = getId('blk').value) req += "&b="+encodeURIComponent(st.replace(/\s/g, ''));
    if (getId('eus').checked || getId('ebl').checked) req += rChecked('e[]','e',1);
    if (st = getId('kwd').value) req += "&keyword="+encodeURIComponent(st.replace(/,/g, ' '));
    if (st = $('input[name="rb"]:checked').val()) req += "&rollback="+encodeURIComponent(st);
    if (st = getId('date').value) {
        req += "&unixtime&t="+Math.floor(new Date(st).getTime()/1000);
        dset = true;
        if (getId('trv').checked) req += "&asendt";
    }
    else dset = false;
    getId("test").innerHTML = req;
}

// for displaying time
function timeago(t) {
    var d,h,m,s,r;
    t = unix-t;
    d = Math.floor(t/86400);
    h = Math.floor(t/3600%24);
    m = Math.floor(t/60%60);
    s = Math.floor(t%60);
    
    if (d > 0) r=d+"d "+h+"h "+m+"m "+s+"s";
    else if (h > 0) r=h+"h "+m+"m "+s+"s";
    else if (m > 0) r=m+"m "+s+"s";
    else if (s > 0) r=s+"s";
    else r="0ms";
    
    return r;
}


// first submit
function getInformation() {
    unix = Date.now()/1000|0;
    compile();
    if (getId('lim').value) lim = getId('lim').value;
    else lim = 30;
    qload("?lim="+lim+req);
    query = lim;
}

// if "load more" is triggered
function getMoreInformation() {
    if (getId('more').value) more = +getId('more').value;
    else more = 10;
    qload("?lim="+more+req+"&offset="+query,1);
    query += more;
    document.getElementById("debug").innerHTML = query;
}

// connect with the conn.php
function qload(request,add) {
    var xmlhttp;
    if (!request) request = '';
    xmlhttp=new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            if (add) document.getElementById("maintbl").innerHTML += phraseReturn(xmlhttp.responseText);
            else document.getElementById("maintbl").innerHTML = phraseReturn(xmlhttp.responseText);
        }
    }
    xmlhttp.open("GET","conn.php"+request,true);
    xmlhttp.send();
}

// returns data in table format
function phraseReturn(obj) {
    var g = JSON.parse(obj),o,r;
    if (g[0]['status']) { // If failed
        /*
        o = '<tr><td colspan="8">';
        switch (g[0]['err']) {
            case 'block':
            o += 'Invalid blocks: '+g[0]['block'].join(', ');
            break;
            case 'username and block':
            o += 'Invalid blocks: '+g[0]['block'].join(', ')+'<br>';
            case 'username':
            o += 'Invalid IGNs: '+g[0]['username'].join(', ');
            break;
            case 'invalid query':
            o += 'The request did not go through properly.  <br>Error '+g[0]['sqlerror'][0]+': '+g[0]['sqlerror'][1]+'<br>MySQL request: '+g[0]['query'];
            break;
            case 'no results':
            o += '<i>No more results</i>'
        }
        o += '</td></tr>';
        */
    }
    else {
        r = g[1];
        o = '';
        for (var i = 0; i<r.length; i++) {
            o += '<tr';
            if (r[i]['rolled_back'] == '1') o += ' class="strikeout"';
            // Time, Username, Action
            o += '><td title="'+new Date(r[i]['time']*1000)+'">'+timeago(r[i]['time'])+'</td><td>'+r[i]['user']+'</td><td>'+r[i]['table']+'</td><td';
            switch(r[i]["table"]) {
                case "click":
                    r[i]['rolled_back'] = "";
                case "container":
                case "block":
                case "kill":
                    // rolled_back translation
                    if(r[i]['rolled_back']) {
                        if(r[i]['rolled_back'] == "0") r[i]['rolled_back'] = "Not rolled.";
                        else if(r[i]['rolled_back'] == "1") r[i]['rolled_back'] = "Rolled.";
                    }
                case "session":
                    // Coordinates, Type:Data, Amount, Rollback
                    o += '>'+r[i]['x']+' '+r[i]['y']+' '+r[i]['z']+' '+r[i]['wid']+'</td><td>'+r[i]['type']+':'+r[i]['data']+'</td><td'+((r[i]['action'] == "0")?' class="bg-warning">-':((r[i]['action'] == "1")?' class="bg-info">+':'>'))+((r[i]['table'] == "container") ? r[i]['amount'] : '')+'</td><td>'+r[i]['rolled_back'];
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
    return o;
}

ready();
</script>
<p>Index last updated  Jan 22, 2016.  Version 0.5.3-alpha</p>
<p>This web app utilizes <a href="http://v4-alpha.getbootstrap.com/">Bootstrap v4</a> and <a href="https://eonasdan.github.io/bootstrap-datetimepicker/"> Bootstrap Datepicker v4</a>.<br>&copy; SimonOrJ, 2015-<?=date("Y")?>.  All Rights Reserved.</p>
</body>
</html>
