(function () {
// CoLWI v0.9.0
// Form-handler JavaScript
// Copyright (c) 2015-2016 SimonOrJ

// this uses jQuery and jQuery UI.

"use strict";

// All the used DOM references in an object
var $lookup     = $("#lookup"),
    $coord      = {
        c1Label:    $("#lCorner1"),
        c2Label:    $("#lCorner2"),
        radiusHide: $(".lRadiusHide"),
        c1:         $("#lC1"),
        c2:         $("#lC2"),
        x2:         $("#lX2"),
        y2:         $("#lY2"),
        z2:         $("#lZ2")
    },
    $world      = $("#lWorld"),
    $user       = $("#lU"),
    $userRev    = $("label[for=lUEx]"),
    $block      = $("#lB"),
    $blockRev   = $("label[for=lBEx]"),
    $date       = $("#lT"),
    $dateRev    = $("label[for=lTRv]"),
    $server     = $("#lServer"),
    $submit     = $("#lSubmit"),
    $moreSubmit = $("#mSubmit"),
    $table      = $("#outputTable"),
    $pages      = $("#row-pages"),
    c;

// Get configuration first, then load the configuration-variable-sensetitive things.
$.getJSON("config.json", function(data) {
    c = data;
    // so out-table can use it
    window.c = c;
    $date.datetimepicker({format:c.form.dateFormat+" "+c.form.timeFormat});
});

$('[data-toggle="tooltip"]').tooltip();

// Radius/Corners toggle
function radius(boolCorner) {
    if(($coord.c1Label.text() === "Center")||boolCorner) {
        $coord.c1Label.text("Corner 1");
        $coord.c2Label.text("Corner 2");
        $coord.c2.addClass("input-group");
        $coord.radiusHide.show();
        $coord.x2.attr("placeholder","x");
    }
    else {
        $coord.c1Label.text("Center");
        $coord.c2Label.text("Radius");
        $coord.c2.removeClass("input-group");
        $coord.y2.val("");
        $coord.z2.val("");
        $coord.radiusHide.hide();
        $coord.x2.attr("placeholder","Radius");
    }
}
$("#lRCToggle").click(function(){radius();});

// Some CSV Function
var csv = {
    append: function (value, add) {
        var a = value.split(/, ?/);
        return $.inArray(add,a) === -1 ? csv + ", " + add : csv;
    },
    array: function (value) {
        return value.split(/, ?/);
    },
    joinArray: function (array) {
        return array.join(", ");
    }
}

// Autocomplete
var queryTable;
$(".autocomplete")
    // don't navigate away from the field on tab when selecting an item
    .bind( "keydown", function( e ) {
        queryTable = this.getAttribute("data-query-table");
        if ( e.keyCode === $.ui.keyCode.TAB &&
            $( this ).autocomplete( "instance" ).menu.active ) {
            e.preventDefault();
        }
    })
    .autocomplete({
        source: function( request, response ){
            var a = csv.array(request.term);
            $.ajax("autocomplete.php",{
                data: {
                    s : $server.val(),
                    a : queryTable,
                    b : a.pop(),
                    e : a,
                    l : 6
                },
                dataType: "json",
                method: "POST",
                success: function(data){
                    response(data);
                }
            });
        },
        focus: function( e, ui ) {
            var terms = csv.array(this.value);
            terms.pop();
            terms.push( ui.item.value );
            this.value = csv.joinArray(terms);
            return false;
        },
        select: function( e, ui ) {
            var terms = csv.array(this.value);
            terms.pop();
            terms.push( ui.item.value );
            this.value = csv.joinArray(terms);
            return false;
        }
    });

// Dropdown Menu Listener
// TODO: Fix this
$table.on("click", ".rDrop .cPointer", function(){
    var $this = $(this),
        $par = $this.parent(),
        val,
        nVal;
    
    if($this.hasClass("t")) {
        nVal = moment($par.parent().attr("data-time"),["x"]).format(c.form.dateFormat+" "+c.form.timeFormat);
        if($this.hasClass("Asc")) {
            $("#trv").prop("checked",true);
            $("[for=trv]").addClass("active");
            $("#date").val(nVal);
        }
        else if($this.hasClass("Desc")) {
            $("#trv").prop("checked",false);
            $("[for=trv]").removeClass("active");
            $("#date").val(nVal);
        }
    }
    else if($this.hasClass("u")) {
        val = $("#usr").val();
        nVal = $par.prev().text();
        if($this.hasClass("Sch")) {
            if($("#eus").prop("checked")){
                $("#eus").prop("checked",false);
                $("[for=eus]").removeClass("active");
                $("#usr").val(nVal);
            }
            else if(val === ""){$("#usr").val(nVal);}
            else {$("#usr").val(csv.append(val,nVal));}
        }
        else if($this.hasClass("ESch")) {
            if(!$("#eus").prop("checked")){
                $("#eus").prop("checked",true);
                $("[for=eus]").addClass("active");
                $("#usr").val(nVal);
            }
            else if(val === ""){$("#usr").val(nVal);}
            else {$("#usr").val(csv.append(val,nVal));}
        }
    }
    else if($this.hasClass("c")) {
        nVal = $par.prev().text().split(" ");
        if($this.hasClass("Fl1")) {
            $("#x1").val(nVal[0]);
            $("#y1").val(nVal[1]);
            $("#z1").val(nVal[2]);
            $("#wid").val(nVal[3]);
        }
        else if($this.hasClass("Fl2")) {
            radius(true);
            $("#x2").val(nVal[0]);
            $("#y2").val(nVal[1]);
            $("#z2").val(nVal[2]);
            $("#wid").val(nVal[3]);
        }
        else if($this.hasClass("DMap")) {
            window.open(s.dynmap.URL+"?worldname="+nVal[3]+"&mapname="+s.dynmap.map+"&zoom="+s.dynmap.zoom+"&x="+nVal[0]+"&y="+nVal[1]+"&z="+nVal[2],"CoLWI-dmap");
        }
    }
    else if($this.hasClass("b")) {
        val = $("#blk").val();
        nVal = $par.parent().attr("data-block");
        if($this.hasClass("Sch")) {
            if($("#ebl").prop("checked")){
                $("#ebl").prop("checked",false);
                $("[for=ebl]").removeClass("active");
                $("#blk").val(nVal);
            }
            else if(val === ""){$("#blk").val(nVal);}
            else {$("#blk").val(csv.append(val,nVal));}
        }
        else if($this.hasClass("ESch")) {
            if(!$("#ebl").prop("checked")){
                $("#ebl").prop("checked",true);
                $("[for=ebl]").addClass("active");
                $("#blk").val(nVal);
            }
            else if(val === ""){$("#blk").val(nVal);}
            else {$("#blk").val(csv.append(val,nVal));}
        }
    }
});

// Purge server cache button
$("#purgeServerCache").click(function() {
    var server = $("#lServer").val();
    if(confirm("Do you want to purge \""+server+"\" server's cache? You won't lose any permanent data.")) {
        $.ajax("purge.php?server="+server,{
            dataType:"json",
            success:function(data){
                if(data[0]) {alert("The \""+server+"\" server's cache was cleared successfully.");}
                else {alert("Unfortunately, the purge of \""+server+"\" server's cache was unsuccessful."+(data[1]?"\nReason: "+data[1]:""));}
            }
        });
    }
});

// Purge all cache button
$("#purgeAllCache").click(function() {
    if(confirm("Do you want to purge all of the cache? You won't lose any permanent data.")) {
        $.ajax("purge.php?all=on",{
            dataType:"json",
            success:function(data){
                if(data[0]) {alert("The ./cache/ directory was cleared successfully.");}
                else {alert("Unfortunately, the purge was unsuccessful."+(data[1]?"\nMessage"+data[1]:""));}
            }
        });
    }
});
}());