(function () {
// CoLWI v0.9.0
// Form-handler JavaScript
// Copyright (c) 2015-2016 SimonOrJ

// this uses jQuery and jQuery UI.

"use strict";

// All the used DOM references in an object
var $form       = $("#lookup"),
    $coord      = {
        c1Label:    $("#lCorner1"),
        c2Label:    $("#lCorner2"),
        radiusHide: $(".lRadiusHide"),
        c1:         $("#lC1"),
        c2:         $("#lC2"),
        x1:         $("#lCX"),
        y1:         $("#lCY"),
        z1:         $("#lCZ"),
        x2:         $("#lCX2"),
        y2:         $("#lCY2"),
        z2:         $("#lCZ2")
    },
    $world      = $("#lWorld"),
    $user       = $("#lU"),
    $block      = $("#lB"),
    $date       = $("#lT"),
    $reverse    = {
        user: {
            button: $("label[for=lUEx]"),
            box: $("#lUEx")
        },
        block: {
            button: $("label[for=lBEx]"),
            box: $("#lBEx")
        },
        date: {
            button: $("label[for=lTRv]"),
            box: $("#lTRv")
        }
    },
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
    
    $date.val(moment($date.val(), ["X", "YYYY-MM-DDTHH:mm", c.form.dateFormat+" "+c.form.timeFormat], true)
        .format(c.form.dateFormat+" "+c.form.timeFormat));
    
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
    } else {
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
    append: function (text, value) {
        return $.inArray(value, text.split(/, ?/)) === -1 ? text + ", " + value : text;
    },
    array: function (value) {
        return value.split(/, ?/);
    },
    join: function (array) {
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
            this.value = csv.join(terms);
            return false;
        },
        select: function( e, ui ) {
            var terms = csv.array(this.value);
            terms.pop();
            terms.push( ui.item.value );
            this.value = csv.join(terms);
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
        nVal = moment($par.parent().attr("data-time"),"X").format(c.form.dateFormat+" "+c.form.timeFormat);
        if($this.hasClass("Asc")) {
            $reverse.date.box.prop("checked",true);
            $reverse.date.button.addClass("active");
        } else if($this.hasClass("Desc")) {
            $reverse.date.box.prop("checked",false);
            $reverse.date.button.removeClass("active");
        }
        $date.val(nVal);
    } else if($this.hasClass("u")) {
        val = $user.val();
        nVal = $par.prev().text();
        if($this.hasClass("Sch")) {
            if($reverse.user.box.prop("checked")){
                $reverse.user.box.prop("checked",false);
                $reverse.user.button.removeClass("active");
                $user.val(nVal);
            } else if(val === ""){
                $user.val(nVal);
            } else {
                $user.val(csv.append(val,nVal));
            }
        } else if($this.hasClass("ESch")) {
            if(!$reverse.user.box.prop("checked")){
                $reverse.user.box.prop("checked",true);
                $reverse.user.button.addClass("active");
                $user.val(nVal);
            } else if(val === ""){
                $user.val(nVal);
            } else {
                $user.val(csv.append(val,nVal));
            }
        }
    }
    else if($this.hasClass("c")) {
        nVal = $par.prev().text().split(" ");
        if($this.hasClass("Fl1")) {
            $coord.x1.val(nVal[0]);
            $coord.y1.val(nVal[1]);
            $coord.z1.val(nVal[2]);
            $world.val(nVal[3]);
        } else if($this.hasClass("Fl2")) {
            radius(true);
            $coord.x2.val(nVal[0]);
            $coord.y2.val(nVal[1]);
            $coord.z2.val(nVal[2]);
            $world.val(nVal[3]);
        } else if($this.hasClass("DMap")) {
            window.open(s.dynmap.URL+"?worldname="+nVal[3]+"&mapname="+s.dynmap.map+"&zoom="+s.dynmap.zoom+"&x="+nVal[0]+"&y="+nVal[1]+"&z="+nVal[2],"CoLWI-dmap");
        }
    }
    else if($this.hasClass("b")) {
        val = $block.val();
        nVal = $par.parent().attr("data-block");
        if($this.hasClass("Sch")) {
            if($reverse.block.box.prop("checked")){
                $reverse.block.box.prop("checked",false);
                $reverse.block.button.removeClass("active");
                $block.val(nVal);
            } else if (val === "") {
                $block.val(nVal);
            } else {
                $block.val(csv.append(val,nVal));
            }
        }
        else if($this.hasClass("ESch")) {
            if(!$reverse.block.box.prop("checked")){
                $reverse.block.box.prop("checked",true);
                $reverse.block.button.addClass("active");
                $block.val(nVal);
            } else if (val === "") {
                $block.val(nVal);
            } else {
                $block.val(csv.append(val,nVal));
            }
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