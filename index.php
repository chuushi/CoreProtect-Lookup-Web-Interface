<!DOCTYPE html>
<!--
// Developed by SimonOrJ.  Please do not modify.
// Alpha stage
-->
<html>
<head>
<meta charset="utf-8">
<title>CorePortect Web Lookup Interface &bull; by SimonOrJ</title>
<style>
* {margin:0;padding:0}
body {
    color:#444;
    background-color:#C2C2BD;
    font-family:"Gill Sans", "Gill Sans MT", "Myriad Pro", "DejaVu Sans Condensed", Helvetica, Arial, sans-serif;
}
.center {
    text-align: center;
}

#lookuptable {
    max-width:640px;
}

input[type=text], input[type=number], input[type=datetime-local], label.flexfield, label.stretchfield {
    background-color: #DBDBD6;
    font-size: 16px;
    font-weight: normal;
    padding: .5em;
    border-style: none;
    align-items: center;
    justify-content: center;
}

form.json label input[type="checkbox"] { /* json = javascript on */
    display: none;
}

form.json label.checked {
    background-color: #ff6139;
}

.sectionwrapper{
    border: 1px solid;
    margin: 0 4px 4px;
}

form .head {
    display: block;
    text-align: left;
    font-weight: bold;
    cursor: default;
}
.flexwrapper {
    display: flex;
}
.flexfield {
    white-space: nowrap;
    display:inline-flex;
    flex-grow: 1;
    width: 0;
}
.flexexclude {
    flex-basis: auto;
    -webkit-flex-basis: auto;
    flex-grow: 0;
    width: 5em;
}
.stretchfield {
    display:block;
    width: 100%;
    box-sizing: border-box;
}

.flexfield:hover, .stretchfield:hover {
    background-color: #EEE;
}
label.flexfield:active, label.stretchfield:active {
background-color: #ff6139;
}

.tooltip:hover:after { content: attr(data-tooltip); position: absolute; white-space: nowrap; background: rgba(0, 0, 0, 0.85); padding: 3px 7px; color: #FFF; border-radius: 3px; -moz-border-radius: 3px; -webkit-border-radius: 3px; margin-left: 7px; margin-top: -3px;}

/* Result Table */
table {
    border-collapse: collapse;
}

td {
    position: relative;
}

tr.strikeout td:before {
    content: " ";
    position: absolute;
    top: 50%;
    left: 0;
    border-bottom: 1px solid #111;
    width: 100%;
}

#output {
    width: 100%;
}

</style>
</head>
<body>

<h1>CoreProtect Web Lookup Interface</h1>
<p>by SimonOrJ</p>
<p>This project is still undergoing alpha testing.  Please report any problems or feedback to the <a href="https://github.com/SimonOrJ/CoreProtect-Lookup-Web-Interface">GitHub project page</a>.</p>
<p>Thank you for testing with me! ~SimonOrJ</p>
<div id="test"></div>
<div id="debug"></div>
<form name="lookup" id="lookup" action="javascript:getInformation();">

<!--
- time(t) in seconds and t2
- block(b)
- chat/command/sign search (keyword)
- exclude (e) players or blocks.

a inputs:
'block','chat','click','command','container','kill','session','username'
-->
<table id="lookuptable">
  <tr>
    <th rowspan="2">
      <span class="head">Actions</span>
      <div class="sectionwrapper">
      <label class="stretchfield" for="abl" onClick="checkClick(this)"><input type="checkbox" id="abl" name="a[]" value="block" checked> Block</label>
      <label class="stretchfield" for="acl" onClick="checkClick(this)"><input type="checkbox" id="acl" name="a[]" value="click"> Click</label>
      <label class="stretchfield" for="acn" onClick="checkClick(this)"><input type="checkbox" id="acn" name="a[]" value="container"> Container</label>
      <label class="stretchfield" for="ach" onClick="checkClick(this)"><input type="checkbox" id="ach" name="a[]" value="chat"> Chat</label>
      <label class="stretchfield" for="aco" onClick="checkClick(this)"><input type="checkbox" id="aco" name="a[]" value="command"> Command</label>
      <label class="stretchfield" for="aki" onClick="checkClick(this)"><input type="checkbox" id="aki" name="a[]" value="kill"> Kill</label>
      <label class="stretchfield" for="ase" onClick="checkClick(this)"><input type="checkbox" id="ase" name="a[]" value="session"> Session</label>
      <label class="stretchfield" for="aus" onClick="checkClick(this)"><input type="checkbox" id="aus" name="a[]" value="username"> Username</label>
      </div>
  </th>
    <th>
      <span" class="head">Center / Corner 1</span>
      <div class="sectionwrapper flexwrapper">
      <input class="flexfield center" type="number" id="x1" name="xyz[]" placeholder="x"><input class="flexfield center" type="number" id="y1" name="xyz[]" placeholder="y"><input class="flexfield center" type="number" id="z1" name="xyz[]" placeholder="z">
      </div>
    </th>
  </tr>
  <tr>
    <td>
      <span class="head">Radius / Corner 2</span>
      <div class="sectionwrapper flexwrapper">
      <input class="flexfield center" type="number" id="x2" name="xyz2[]" placeholder="Radius or x"><input class="flexfield center" type="number" id="y2" name="xyz2[]" placeholder="y"><input class="flexfield center" type="number" id="z2" name="xyz2[]" placeholder="z">
      </div>
    </td>
  </tr>
  <tr>
    <td colspan="2">
      <label for="usr" class="head">Users to search</label>
      <div class="sectionwrapper flexwrapper">
      <label class="flexfield flexexclude" for="eusr" onClick="checkClick(this)"><input type="checkbox" id="eusr" name="eu"> Exclude</label><input class="flexfield" type="text" pattern="((#[a-zA-Z_]+)|([a-zA-Z0-9_]{2,16}))(,\s?((#[a-zA-Z_]+)|([a-zA-Z0-9_]{2,16})))*" id="usr" name="u" placeholder="Separate by single comma(,)">
      </div>
    </td>
  </tr>
  <tr>
    <td colspan="2">
      <label for="blk" class="head">Blocks to search</label>
      <div class="sectionwrapper flexwrapper"><label class="flexfield flexexclude" for="eblk" onClick="checkClick(this)"><input type="checkbox" id="eblk" name="e"> Exclude</label><input class="flexfield" type="text" id="blk" name="b" placeholder="minecraft:<block> - Separate by single comma(,)"></div>
    </td>
  </tr>
  <tr>
    <td colspan="2">
      <label for="kwd" class="head">Keyword for chat/command lookup</label>
      <div class="sectionwrapper"><input class="stretchfield" type="text" id="kwd" name="keyword" placeholder="Separate by single comma(,)"></div>
</td>
  </tr>
  <tr>
    <td>
      <label for="date" class="head">Date From</label>
      <div class="sectionwrapper"><input class="stretchfield" type="datetime-local" pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}T[0-9]{2}:[0-9]{2}(:[0-9]{2})?" placeholder="0000-00-00T00:00:00" id="date" name="date"></div>
</td>
    <td>
      <label for="lim" class="head">Limit query</label>
      <div class="sectionwrapper"><input class="stretchfield" type="number" id="lim" name="lim" placeholder="30"></div>
    </td>
  </tr>
  <tr>
    <td colspan="2">
      <input type="submit" value="Update">
      <input type="reset" value="Reset">
    </td>
  </tr>
</table>
</form>

<table id="output">
  <thead>
  <tr><th>Seconds Ago</th><th>User</th><th>Search Action</th><th>Coordinates / World</th><th>Block/Item:Data</th><th>Amount</th><th>Action</th><th>Rolled back?</th></tr>
  </thead>
  <tbody id="maintbl"><tr><td colspan="9">Please submit a lookup.</td></tr></tbody>
</table>
<form name="getmore" action="javascript:getMoreInformation();">
<label for="more">load next </label><input type="number" id="more" name="more" placeholder="10"><br>
<input type="submit" value="Load more">
</form>


<script>
getId = function(val) {return document.getElementById(val)},
getForm = function(val) {return document.lookup[val]};

/*
 * Styling
 */
function ready() {
    getId('lookup').className = "json";
    getId('achid').classList.add('checked')
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
//    var a,b,date,e,r,u,lim,xyz,xyz2;
    var rChecked = function(val,setv,check) { // This function has a bad name...
            var ls = [],res;
            for (var i=0; i < getForm(val).length; i++) if(getForm(val)[i].checked || !check) ls.push(getForm(val)[i].value);
            res = ls.join();
            return "&"+setv+'='+res;
    }
    
    req = rChecked("a[]",'a',1);
    if (getId('x1').value && getId('y1').value && getId('z1').value) {
        req += rChecked("xyz[]",'xyz');
        if (getId('x2').value && getId('y2').value && getId('z2').value) req += rChecked("xyz2[]",'xyz2');
        else if (getId('x2').value) req += rChecked('xyz2[]','r');
        else req += "&r=0"
    }
    if (st = getId('usr').value) {
        if (getId('eusr').checked) req += "&eu="+encodeURIComponent(st.replace(/\s/g, ''));
        else req += "&u="+encodeURIComponent(st.replace(/\s/g, ''));
    }
    if (st = getId('blk').value) {
        if (getId('eblk').checked) req += "&e="+encodeURIComponent(st.replace(/\s/g, ''));
        else req += "&b="+encodeURIComponent(st.replace(/\s/g, ''));
    }
    if (st = getId('kwd').value) req += "&keyword="+encodeURIComponent(st.replace(/,/g, ' '));
    if (st = getId('date').value) {
        req += "&date="+Math.floor(new Date(st).getTime()/1000);
        dset = true;
    }
    else dset = false;
//    document.getElementById("debug").innerHTML = getTime(getId('time').value);
//    if (t == "")
//    qtime = Date.now() / 1000;
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
    var g = JSON.parse(obj),
        o = '',
        r;
    if (g[0]['status']) { // If failed
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
    }
    else {
        r = g[1];
        if (!dset) { // Conditional statement for locking search time into request
            req += '&date='+r[0]['time'];
            dset = true;
        }
        for (var i = 0; i<r.length;i++) {
            if (r[i]['rolled_back'])  {
                if (r[i]['rolled_back'] == 1) r[i]['rolled_back'] = "Rolled.";
                else r[i]['rolled_back'] = "Not Rolled.";
            }
            
            o += '<tr';
            if (r[i]['rolled_back'] == 'Rolled.') o += ' class="strikeout"';
            o += '><td title="'+new Date(r[i]['time']*1000)+'">'+timeago(r[i]['time'])+'</td><td>'+r[i]['user']+'</td><td>'+r[i]['dbtable']+'</td><td';
            if (['block','click','kill','container'].indexOf(r[i]['dbtable'])>=0) {
                o += '>'+r[i]['x']+' '+r[i]['y']+' '+r[i]['z']+' '+r[i]['wid']+'</td><td>'+r[i]['type'][2].replace(/^minecraft:/,"")+':'+r[i]['data']+'</td><td>'+r[i]['amount']+'</td><td>';
                switch (r[i]['action']) {
                    case '0':
                    o += 'removed';
                    break;
                    case '1':
                    o += 'placed';
                    break;
                    case '2':
                    o += 'clicked';
                    break;
                    case '3':
                    o += 'killed';
                }
                o += '</td><td>'+r[i]['rolled_back'];
            }
            else if (['chat','command', 'username'].indexOf(r[i]['dbtable'])>=0) o += ' colspan="5">'+r[i]['data'];
            else if (r[i]['dbtable'] == 'session') {
                o += '>'+r[i]['x']+' '+r[i]['y']+' '+r[i]['z']+' '+r[i]['wid']+'</td><td colspan="4">';
                if (r[i]['action'] == 1) o += 'logged in';
                else o += 'logged out'
            }
            else o += ' colspan="5">NULL';
            
            o +='</td></tr>';
        }
    }
    return o;
}

ready();
</script>
<p>Index last updated  Oct 24, 2015.  Version 0.3.0.1-alpha</p>
<p>&copy; SimonOrJ, 2015.  All Rights Reserved.</p>
</body>
</html>
