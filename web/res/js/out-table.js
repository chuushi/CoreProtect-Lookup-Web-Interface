/* CoreProtect LWI - v0.8.1-beta
 * Javascript code by SimonOrJ.
 * this uses jQuery.
 */
function outTable(){
"use strict";
var resCt,intCt;
// Main Submit
$("#lookup").submit(function($thislookup) {
    $thislookup.preventDefault();
    $.ajax("conn.php",{
      beforeSend:function(xhr,s){$("#submitBtn").prop("disabled",true);if($("#date").val()!==""){s.data+="&t="+moment($("#date").val(),$dateFormat+" "+$timeFormat).format("X");}},
      data:$("#lookup").serialize(),
      dataType:"json",
      method:"POST",
      complete:function(){$("#submitBtn").prop("disabled",false);},
      success:function(data){$("#row-pages").html('<li class="nav-item"><a class="nav-link active" href="#top">Top</a></li><li class="nav-item"><a class="nav-link" href="#row-0">0</a></li>');reachedLimit(false);$lastDataTime = Date.now();resCt=1;intCt=$pageInterval;phraseReturn(data);},
      error: function(){$("#row-pages").html('<li class="nav-item"><a class="nav-link active" href="#top">Top</a></li>');phraseReturn([{
        status:7,
        reason:"The lookup script was unable to send a proper response."
      }]);}
    });
});

// More Submit
$("#loadMore").submit(function($thislookup) {
    $thislookup.preventDefault();
    $.ajax("conn.php",{
      data:$("#loadMore").serialize(),
      dataType:"json",
      method:"POST",
      complete:function(){},
      success:function(data){phraseReturn(data,1);},
    });
});

function reachedLimit(toggle) {
  $("#loadMoreBtn").prop("disabled",toggle);
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
$("#output").on("show.bs.dropdown",".rDrop",function(){
    if(!$(this).hasClass("dropdown")) {
        $(this).addClass("dropdown");
        if($(this).hasClass("t")){$(this).append('<div class="dropdown-menu"><span class="dropdown-header">Date/Time</span><span class="dropdown-item cPointer t Asc">Search ascending</span><span class="dropdown-item cPointer t Desc">Search descending</span></div>');}
        else if($(this).hasClass("u")){$(this).append('<div class="dropdown-menu"><span class="dropdown-header">User</span><span class="dropdown-item cPointer u Sch">Search user</span><span class="dropdown-item cPointer u ESch">Exclusive Search</span></div>');}
        else if($(this).hasClass("c")){
            $(this).append('<div class="dropdown-menu"><span class="dropdown-header">Coordinates</span><span class="dropdown-item cPointer c Fl1">Center/Corner 1</span><span class="dropdown-item cPointer c Fl2">Corner 2</span>'+($dynmapURL?'<span class="dropdown-item cPointer c DMap">Open in Dynmap</span>':"")+'</div>');
        }
        else if($(this).hasClass("b")){$(this).append('<div class="dropdown-menu"><span class="dropdown-header">Block</span><span class="dropdown-item cPointer b Sch">Search block</span><span class="dropdown-item cPointer b ESch">Exclusive Search</span></div>');}
        else{$(this).append('<div class="dropdown-menu"><span class="dropdown-header">derp</span></div>');}
    }
});

// Displaying sign data function
$("#output").on("click.collapse-next.data-api",".collapse-toggle",function(){$(this).next().collapse("toggle");});

// returns data in table format
var spanSign = '<span class="collapse-toggle" data-toggle="collapse-next" aria-expanded="false">',
divSignData = function(Lines) {return '<div class="mcSign">'+Lines.join("&nbsp;\n&nbsp;")+"</div>";},
spanDToggle =  '<span class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">',
$lastDataTime;
function phraseReturn(obj,more) {
    $("#genTime").text("Request generated in "+Math.round(obj[0].duration*1000)+"ms");
    var o;
    if (obj[0].status) { // If failed
        o = '<tr class="text-'+(obj[0].status===1?"info":"danger")+'"><th scope="row">'+(obj[0].status===1?"--":"E")+'</th><td colspan="7"';
        switch(obj[0].status) {
            case 1:
                o += ' class="text-xs-center">'+reachedLimit(true);
            break;
            case 2:
                o += '><b>The request did not go through properly.</b></td></tr><tr><th scope="row">-</th><td>'+obj[1][0]+"</td><td>"+obj[1][1]+'</td><td colspan="7">Error '+obj[1][2];
                reachedLimit(true);
            break;
            case 3:
                o += '><b>The webserver could not establish a connection to the database.</b> Please check your settings.</td></tr><tr><th scope="row">-</th><td colspan="7">PDO Exception: '+obj[1];
                
            break;
            case 4:
                o += "><b>The following value does not exist in the CoreProtect's database:</b>";
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
            default:
                o += '><b>Unexpected Error '+obj[0].status+":</b> "+obj[0].reason;
                reachedLimit(true);
                break;
        }
        o += '</td></tr>';
    }
    else { // Success
        if(more){$("#offset").val(parseInt($("#offset").val())+parseInt(if_exist($("#moreLim").val(),30)));}
        else { // Set form values for offset lookup
            $("#SQL").val(obj[0].SQL);
            $("#SQLqs").val(obj[0].SQLqs);
            $("#offset").val(parseInt(if_exist($("#lim").val(),30)));
        }
        var r = obj[1];
        o = "";
        var i;
        for (i = 0; i<r.length; i++) {
            // UNIX to JS Date
            r[i].time *= 1000;
            if($timeDividor < Math.abs($lastDataTime-r[i].time)||!moment($lastDataTime).isSame(r[i].time,"day")){o += '<tr class="table-section"><th scope="row">-</th><th colspan="7">'+moment(r[i].time).calendar(null,{
                sameDay: "[Today at] "+$timeFormat,
                lastDay: "[Yesterday at] "+$timeFormat,
                lastWeek: "[Last] dddd, "+$dateFormat+" "+$timeFormat,
                sameElse: $dateFormat+" "+$timeFormat
            })+"</th></tr>";}
            o += '<tr id="row-'+resCt+'"';
            if (r[i].rolled_back === "1"){o += ' class="table-success"';}

            // Time, Username, Action
            o += '><th scope="row">'+resCt+'</th><td class="rDrop t" title="'+moment(r[i].time).format($dateFormat)+'" data-time="'+r[i].time+'">'+spanDToggle+moment(r[i].time).format($timeFormat)+'</span></td><td class="rDrop u">'+spanDToggle+r[i].user+'</span></td><td>'+r[i].table+'</td><td';
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
            }
            o +='</td></tr>';
            resCt++;
        }
    }
    if(more){$("#mainTbl").append(o);}
    else{$("#mainTbl").html(o);}
    for(intCt;intCt<resCt;intCt=intCt+$pageInterval){
      $("#row-pages").append('<li class="nav-item"><a class="nav-link" href="#row-'+intCt+'">'+intCt+'</a></li>');
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
}
outTable();