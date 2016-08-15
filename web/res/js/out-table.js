(function(){
// CoLWI v0.9.0
// Table-formatting JavaScript
// Copyright (c) 2015-2016 SimonOrJ

// This uses jQuery.
// TODO: Merge this with form-handler.js

"use strict";

// All the used DOM references in an object
var $form       = $("#lookupForm"),
    $date       = $("#lT"),
    $server     = $("#lServer"),
    $limit      = $("#lLimit"),
    $submit     = $("#lSubmit"),
    $moreForm   = $("#loadMoreForm"),
    $moreLimit  = $("#mLimit"),
    $moreOffset = $("#mOffset"),
    $moreSubmit = $("#mSubmit"),
    $table      = $("#outputTable"),
    $pages      = $("#row-pages"),
    s           = {server: ""};

// Variables
var formData = window.location.href.split('?',2)[1],
    resCt, intCt;

// Load dynmap information
function loadDynmapConfig (server) {
    if (server !== s.server) {
        $.getJSON("server/"+server+".json", function (data) {
            s = data;
            s.server = server;
        }).error(function () {
            s = {server: server};
        }).complete(function() {
            // So form-handler can use it
            window.s = s;
        });
    }
}

// On lookup form submit
$form.submit(function(e) {
    e.preventDefault();
    $.ajax("conn.php",{
        beforeSend: function(xhr,s){
            // Disable submit button
            $submit.prop("disabled",true);
            
            // Get the time in UNIX
            if ($date.val()!=="") {
                s.data += "&t="
                    + moment($date.val(),c.form.dateFormat+" "+c.form.timeFormat).format("X");
            }
            
            // Set offset
            $moreOffset.val($limit.val() !== "" ? parseInt($limit.val()) : 30);
            
            // Set the Load More action
            var lim = "lim";
            formData = $.param($.grep($form.serializeArray(),function(e){return e.name !== lim}));
        },
        data:       $form.serialize(),
        dataType:   "json",
        method:     "POST",
        complete:   function() {
            // Upon submit
            $submit.prop("disabled",false);
            $moreSubmit.prop("disabled",false);
            loadDynmapConfig($server.val());
        },
        success:function(data){
            // Start the page count on bottom bar
            $pages.html('<li class="nav-item"><a class="nav-link active" href="#top">Top</a></li><li class="nav-item"><a class="nav-link" href="#rRow-0">0</a></li>');
            reachedLimit(false);
            
            // Revise "action" to the loadMore form
            $moreForm[0].setAttribute("action","./?"+formData)
            $lastDataTime = Date.now();
            resCt=1;
            intCt=c.form.pageInterval;
            phraseReturn(data);
        },
        error: function(){
            $pages.html('<li class="nav-item"><a class="nav-link active" href="#top">Top</a></li>');
            phraseReturn([{
                status:7,
                reason:"The lookup script was unable to send a proper response."
            }]);
        }
    });
});

// on Load More submit
$moreForm.submit(function(e) {
    e.preventDefault();
    $.ajax("conn.php?" + $moreForm.attr("action").split("?",2)[1],{
        beforeSend: function() {
            // Disable submit button
            $submit.prop("disabled",true);
            $moreSubmit.prop("disabled",true);
            
            // Add Offset
            $moreOffset.val(parseInt($moreOffset.val()) + ($moreLimit.val() !== "" ? parseInt($moreLimit.val()) : 10));
        },
        data:$moreForm.serialize(),
        dataType:"json",
        method:"POST",
        complete:function(){
            $submit.prop("disabled",false);
            $moreSubmit.prop("disabled",false);
        },
        success:function(data) {
            phraseReturn(data,1);
        },
    });
});

// Limit function
function reachedLimit(toggle) {
  $moreSubmit.prop("disabled",toggle);
  if(toggle) {
      return '<i>No more results</i>';
  }
}

// Simple exist function
function if_exist(value,if_not) {
    if(value==="") {return if_not;}
    return value;
}

// Dropdown menu creation function
$table.on("show.bs.dropdown",".rDrop",function(){
    if(!$(this).hasClass("dropdown")) {
        $(this).addClass("dropdown");
        if ($(this).hasClass("t")) {
            // Time
            $(this).append('<div class="dropdown-menu"><span class="dropdown-header">Date/Time</span><span class="dropdown-item cPointer t Asc">Search ascending</span><span class="dropdown-item cPointer t Desc">Search descending</span></div>');
        } else if($(this).hasClass("u")) {
            // Username
            $(this).append('<div class="dropdown-menu"><span class="dropdown-header">User</span><span class="dropdown-item cPointer u Sch">Search user</span><span class="dropdown-item cPointer u ESch">Exclusive Search</span></div>');
        } else if($(this).hasClass("c")) {
            // Coordinates
            $(this).append('<div class="dropdown-menu"><span class="dropdown-header">Coordinates</span><span class="dropdown-item cPointer c Fl1">Center/Corner 1</span><span class="dropdown-item cPointer c Fl2">Corner 2</span>'+(s.dynmap.URL ? '<span class="dropdown-item cPointer c DMap">Open in Dynmap</span>' : "")+'</div>');
        } else if($(this).hasClass("b")) {
            // Block
            $(this).append('<div class="dropdown-menu"><span class="dropdown-header">Block</span><span class="dropdown-item cPointer b Sch">Search block</span><span class="dropdown-item cPointer b ESch">Exclusive Search</span></div>');
        } else {
            // should not reach this point...
            $(this).append('<div class="dropdown-menu"><span class="dropdown-header">derp</span></div>');
        }
    }
});

// Displaying sign data function
$table.on("click.collapse-next.data-api",".collapse-toggle",function(){
    $(this).next().collapse("toggle");
});

// Returns data in table format
var spanSign = '<span class="collapse-toggle" data-toggle="collapse-next" aria-expanded="false">',
divSignData = function(Lines) {
    return '<div class="mcSign">&nbsp;'+Lines.join("&nbsp;\n&nbsp;")+"&nbsp;</div>";
},
spanDToggle =  '<span class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">',
$lastDataTime;
function phraseReturn(obj,more) {
    $("#genTime").text("Request generated in "+Math.round(obj[0].duration*1000)+"ms");
    var o;
    if (obj[0].status !== 0) { // If an error exists
        o = '<tr class="text-'+(obj[0].status===1?"info":"danger")+'"><th scope="row">'+(obj[0].status===1?"--":"E")+'</th><td colspan="7"';
        
        // Error type
        switch(obj[0].status) {
            case 1:
                // End of result
                o += ' class="text-xs-center">'+reachedLimit(true);
            break;
            case 2:
                // SQL unsuccessful
                o += '><b>The request did not go through properly.</b></td></tr><tr><th scope="row">-</th><td>'+obj[1][0]+"</td><td>"+obj[1][1]+'</td><td colspan="7">Error '+obj[1][2];
                reachedLimit(true);
            break;
            case 3:
                // Database Connection failed (PDOException)
                o += '><b>The webserver could not establish a connection to the database.</b> Please check your configuration.</td></tr><tr><th scope="row">-</th><td colspan="7">PDOException: '+obj[1];
                
            break;
            case 4:
                // Some searches by blocks and usernames does not exist
                o += "><b>The following value does not exist in the database:</b>";
                for(var j=0; j<obj[1].length;j++) {
                    o += '</td></tr><tr><th scope="row">-</th><td>';
                    switch(obj[1][j][0]) {
                        // [material,id or value, thing that has weird stuff]
                        case "material":
                            o += 'Block';
                            break;
                        case "user":
                            o += 'Username';
                            break;
                        default:
                            o += obj[1][j][0];
                    }
                    o += '</td><td colspan="6">'+obj[1][j][2];
                }
                reachedLimit(true);
                break;
            case 5:
                // Configuration error
                break;
            case 6:
                // Something else.
            default:
                o += '><b>Unexpected Error '+obj[0].status+":</b> "+obj[0].reason;
                reachedLimit(true);
                break;
        }
        o += '</td></tr>';
    } else {
        // Success
        if(more){$("#offset").val(parseInt($("#offset").val())+parseInt(if_exist($("#moreLim").val(),30)));}
        else { // Set form values for offset lookup
            $("#offset").val(parseInt(if_exist($("#lim").val(),30)));
        }
        var r = obj[1];
        o = "";
        var i;
        for (i = 0; i<r.length; i++) {
            // UNIX to JS Date
            r[i].time *= 1000;
            if(c.form.timeDividor < Math.abs($lastDataTime-r[i].time)||!moment($lastDataTime).isSame(r[i].time,"day")){o += '<tr class="table-section"><th scope="row">-</th><th colspan="7">'+moment(r[i].time).calendar(null,{
                sameDay: "[Today at] " + c.form.timeFormat,
                lastDay: "[Yesterday at] " + c.form.timeFormat,
                lastWeek: "[Last] dddd, " + c.form.dateFormat + " " + c.form.timeFormat,
                sameElse: c.form.dateFormat+" " + c.form.timeFormat
            })+"</th></tr>";}
            o += '<tr id="rRow-'+resCt+'"';
            if (r[i].rolled_back === "1"){o += ' class="table-success"';}

            // Time, Username, Action
            o += '><th scope="row">'+resCt+'</th><td class="rDrop t" title="'+moment(r[i].time).format(c.form.dateFormat)+'" data-time="'+r[i].time+'">'+spanDToggle+moment(r[i].time).format(c.form.timeFormat)+'</span></td><td class="rDrop u">'+spanDToggle+r[i].user+'</span></td><td>'+r[i].table+'</td><td';
            $lastDataTime = r[i].time;
            switch(r[i].table) {
                case "click":
                case "session":
                    r[i].rolled_back = "";
                case "container":
                case "block":
                case "kill":
                    // rolled_back translation
                    if(r[i].rolled_back) {
                        if(r[i].rolled_back === "0"){r[i].rolled_back = "Not rolled.";}
                        else if(r[i].rolled_back === "1"){r[i].rolled_back = "Rolled.";}
                    }
                    // Coordinates, Type:Data, Amount, Rollback
                    o += ' class="rDrop c">'+spanDToggle+r[i].x+' '+r[i].y+' '+r[i].z+' '+r[i].wid+"</span></td><td"+(r[i].table === "session"?">"
                    :(r[i].signdata?' class="rColl">'+spanSign
                    :' class="rDrop b" data-block="'+r[i].type+'">'+spanDToggle)+r[i].type+':'+r[i].data+"</span>"+(r[i].signdata? '<div class="rDrop b collapse" data-block="'+r[i].type+'">'+divSignData(r[i].signdata)+"<br>"+spanDToggle+r[i].type+':'+r[i].data+"</span></div>"
                    :""))+'</td><td'+(r[i].action === "0"?' class="table-warning">-'
                    :(r[i].action === "1"?' class="table-info">+'
                    :'>'))+(r[i].table === "container" ? r[i].amount
                    : '')+'</td><td>'+r[i].rolled_back;
                    break;
                case "chat":
                case "command":
                case "username_log":
                    // Message/UUID
                    o += ' colspan="4">'+r[i].data;
                    break;
            }
            o +='</td></tr>';
            resCt++;
        }
    }
    if (more) {$("#mainTbl").append(o);}
    else {$("#mainTbl").html(o);}
    for (intCt; intCt < resCt; intCt = intCt + c.form.pageInterval) {
        $pages.append('<li class="nav-item"><a class="nav-link" href="#rRow-'+intCt+'">'+intCt+'</a></li>');
    }

/* Smooth scrolling by mattsince87 from http://codepen.io/mattsince87/pen/exByn
// ------------------------------
// http://twitter.com/mattsince87
// ------------------------------
*/
    $('.nav a').click(function(){  
        $('html,body').stop().animate({scrollTop:$($(this).attr('href')).offset().top},380);
        return false;
    });
}
}())
