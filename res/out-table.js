/* CoreProtect LWI - v0.7.0-beta
 * Javascript code by SimonOrJ.
 * this uses jQuery.
 */
function outTable(){
"use strict";

// Main Submit
$("#lookup").submit(function($thislookup) {
    $thislookup.preventDefault();
    $.ajax("conn.php",{
      beforeSend:function(xhr,s){if($("#date").val()!==""){s.data+="&t="+moment($("#date").val(),$dateFormat+" "+$timeFormat).format("X");}},
      data:$("#lookup").serialize(),
      dataType:"json",
      method:"POST",
      complete:function(){},
      success:function(data){reachedLimit(false);$lastDataTime = Date.now();phraseReturn(data);},
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
        if($(this).hasClass("t")){$(this).append('<div class="dropdown-menu"><span class="dropdown-header">Date/Time</span><span class="dropdown-item cPointer t Asc">Search ascending</span><span class="dropdown-item cPointer t Desc">Search descending</span></div>');}
        else if($(this).hasClass("u")){$(this).append('<div class="dropdown-menu"><span class="dropdown-header">User</span><span class="dropdown-item cPointer u Sch">Search user</span><span class="dropdown-item cPointer u ESch">Exclusive Search</span></div>');}
        else if($(this).hasClass("c")){
            $(this).append('<div class="dropdown-menu"><span class="dropdown-header">Coordinates</span><span class="dropdown-item cPointer c Fl1">Center/Corner 1</span><span class="dropdown-item cPointer c Fl2">Corner 2</span>'+($dynmapURL?'<span class="dropdown-item cPointer c DMap">Open in Dynmap</span>':"")+'</div>');
        }
        else if($(this).hasClass("b")){$(this).append('<div class="dropdown-menu"><span class="dropdown-header">Block</span><span class="dropdown-item cPointer b Sch">Search block</span><span class="dropdown-item cPointer b ESch">Exclusive Search</span></div>');}
        else{$(this).append('<div class="dropdown-menu"><span class="dropdown-header">wat</span></div>');}
    }
});
// Dropdown Menu Listener
var dmapWin;
$("#output").on("click",".rDrop .cPointer",function(){
    var $par = $(this).parent(),val,nVal;
    if($(this).hasClass("t")) {
        console.log($par.parent().attr("data-time"));
        nVal = moment($par.parent().attr("data-time"),["x"]).format($dateFormat+" "+$timeFormat);
        if($(this).hasClass("Asc")) {
            $("#trv").prop("checked",true);
            $("[for=trv]").addClass("active");
            $("#date").val(nVal);
        }
        else if($(this).hasClass("Desc")) {
            $("#trv").prop("checked",false);
            $("[for=trv]").removeClass("active");
            $("#date").val(nVal);
        }
    }
    else if($(this).hasClass("u")) {
        val = $("#usr").val();
        nVal = $par.prev().text();
        if($(this).hasClass("Sch")) {
            if($("#eus").prop("checked")){
                $("#eus").prop("checked",false);
                $("[for=eus]").removeClass("active");
                $("#usr").val(nVal);
            }
            else if(val === ""){$("#usr").val(nVal);}
            else {$("#usr").val(val+","+nVal);}
        }
        else if($(this).hasClass("ESch")) {
            if(!$("#eus").prop("checked")){
                $("#eus").prop("checked",true);
                $("[for=eus]").addClass("active");
                $("#usr").val(nVal);
            }
            else if(val === ""){$("#usr").val(nVal);}
            else {$("#usr").val(val+","+nVal);}
        }
    }
    else if($(this).hasClass("c")) {
        nVal = $par.prev().text().split(" ");
        if($(this).hasClass("Fl1")) {
            $("#x1").val(nVal[0]);
            $("#y1").val(nVal[1]);
            $("#z1").val(nVal[2]);
            $("#wid").val(nVal[3]);
        }
        else if($(this).hasClass("Fl2")) {
            radius(true);
            $("#x2").val(nVal[0]);
            $("#y2").val(nVal[1]);
            $("#z2").val(nVal[2]);
            $("#wid").val(nVal[3]);
        }
        else if($(this).hasClass("DMap")) {
            dmapWin = window.open($dynmapURL+"?worldname="+nVal[3]+"&mapname="+$dynmapMapName+"&zoom="+$dynmapZoom+"&x="+nVal[0]+"&y="+nVal[1]+"&z="+nVal[2],"CoLWI-dmap");
        }
    }
    else if($(this).hasClass("b")) {
        val = $("#blk").val();
        nVal = $par.parent().attr("data-block");
        if($(this).hasClass("Sch")) {
            if($("#ebl").prop("checked")){
                $("#ebl").prop("checked",false);
                $("[for=ebl]").removeClass("active");
                $("#blk").val(nVal);
            }
            else if(val === ""){$("#blk").val(nVal);}
            else {$("#blk").val(val+","+nVal);}
        }
        else if($(this).hasClass("ESch")) {
            if(!$("#ebl").prop("checked")){
                $("#ebl").prop("checked",true);
                $("[for=ebl]").addClass("active");
                $("#blk").val(nVal);
            }
            else if(val === ""){$("#blk").val(nVal);}
            else {$("#blk").val(val+","+nVal);}
        }
    }
});

// Displaying sign data function
$("#output").on("click.collapse-next.data-api",".collapse-toggle",function(){$(this).next().collapse("toggle");});

// returns data in table format
var spanSign = '<span class="collapse-toggle" data-toggle="collapse-next" aria-expanded="false">',
divSignData = function(Lines) {return '<div class="mcSign">'+Lines[0]+'<br>'+Lines[1]+'<br>'+Lines[2]+'<br>'+Lines[3]+"</div>";},
spanDToggle =  '<span class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">',
$lastDataTime;
function phraseReturn(obj,more) {
    $("#genTime").text("Request generated in "+Math.round(obj[0].duration*1000)+"ms");
    var o;
    if (obj[0].status) { // If failed
        o = '<tr><td colspan="7"';
        switch(obj[0].status) {
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
                o += "><b>The following value does not exist in the CoreProtect's database:</b></td></tr>";
                for(var j=0; j<obj[1].length;j++) {
                    o += "<tr><td></td><td>";
                    switch(obj[1][j][0]) {
                        // [material,id or value, thing that has weird stuff]
                        case "material":
                            o += 'Block</td><td colspan="5">'+obj[1][j][2];
                            break;
                        case "user":
                            o += 'Username</td><td colspan="5">'+obj[1][j][2];
                            break;
                        default:
                            o += obj[1][j][0]+'</td><td colspan="5">'+obj[1][j][2];
                    }
                    o += "</td></tr>";
                }
                reachedLimit(true);
                break;
            default:
                o += "><b>Unexpected Error "+obj[0].status+":</b> "+obj[0].reason;
                break;
        }
        o += '</td></tr>';
    }
    else { // Success
        if(more){$("#offset").val(parseInt($("#offset").val())+parseInt(if_exist($("#moreLim").val(),10)));}
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
            if($timeDividor < Math.abs($lastDataTime-r[i].time)||!moment($lastDataTime).isSame(r[i].time,"day")){o += '<tr class="table-section"><th colspan="7">'+moment(r[i].time).calendar(null,{
                sameDay: "[Today at]"+$timeFormat,
                nextDay: "[Please Configure Correct Minecraft Server Time]",
                nextWeek: "[Please Configure Correct Minecraft Server Time]",
                lastDay: "[Yesterday at] "+$timeFormat,
                lastWeek: "[Last] dddd, "+$dateFormat,
                sameElse: $dateFormat
            })+"</th></tr>";}
            o += "<tr";
            if (r[i].rolled_back === "1"){o += ' class="table-success"';}

            // Time, Username, Action
            o += '><td class="rDrop t" title="'+moment(r[i].time).format($dateFormat)+'" data-time="'+r[i].time+'">'+spanDToggle+moment(r[i].time).format($timeFormat)+'</span></td><td class="rDrop u">'+spanDToggle+r[i].user+'</span></td><td>'+r[i].table+'</td><td';
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
        }
    }
    if(more){$("#mainTbl").append(o);}
    else{$("#mainTbl").html(o);}
}
}
outTable();