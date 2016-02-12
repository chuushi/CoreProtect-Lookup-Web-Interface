/* CoreProtect LWI - v0.8.1-beta
 * Javascript code by SimonOrJ.
 * this uses jQuery and jQuery UI.
 */
function formHandler() {
"use strict";
$("#date").datetimepicker({format:$dateFormat+" "+$timeFormat});
$("[for=abl]").addClass("active");
$('[data-toggle="tooltip"]').tooltip();

// If URL contains lookup data
if($fm){
    var fixChecked = function(e) {
        if($(e).prop("checked")){$(e).parent().addClass("active");}
        else{$(e).parent().removeClass("active");}
    };
    $("[name='a[]']").each(function() {fixChecked(this);});
    $("[name='e[]']").each(function() {fixChecked(this);});
    $("[name='rollback']").each(function() {fixChecked(this);});
    fixChecked($("#trv"));
    if($PHP_$t){$("#date").val(moment($PHP_$t).format($dateFormat+" "+$timeFormat));}
    if(($("#y2").val()!=="")||($("#z2").val()!=="")) {radius(true);}
}


// Radius/Corners toggle
function radius(boolCorner) {
    if(($("#corner1").text() === "Center")||boolCorner) {
        $("#corner1").text("Corner 1");
        $("#corner2").text("Corner 2");
        $("#c2").addClass("input-group");
        $(".c2").show();
        $("#x2").attr("placeholder","x");
    }
    else {
        $("#corner1").text("Center");
        $("#corner2").text("Radius");
        $("#c2").removeClass("input-group");
        $(".c2").val("");
        $(".c2").hide();
        $("#x2").attr("placeholder","Radius");
    }
}
$("#rcToggle").click(function(){radius();});

// Autocomplete
var qftr;
$( ".autocomplete" )
  // don't navigate away from the field on tab when selecting an item
  .bind( "keydown", function( event ) {
      if ( event.keyCode === $.ui.keyCode.TAB &&
          $( this ).autocomplete( "instance" ).menu.active ) {
        event.preventDefault();
      }
      qftr = $(this).attr("data-qftr");
  })
  .autocomplete({
    source: function( request, response ){
        var a = request.term.split(/, ?/);
        $.ajax("autocomplete.php",{
          data: {
            a : qftr,
            b : a.pop(),
            e : a,
            l : 6
          },
          dataType:"json",
          method:"POST",
          success:function(data){
              response(data);
          }
        });
    },
    focus: function( event, ui ) {
        var terms = this.value.split(/, ?/);
        terms.pop();
        terms.push( ui.item.value );
        this.value = terms.join( ", " );
        return false;
    },
    select: function( event, ui ) {
        var terms = this.value.split(/, ?/);
        terms.pop();
        terms.push( ui.item.value );
        this.value = terms.join( ", " );
        return false;
    }
});

// Dropdown Menu Listener
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
            else {$("#usr").val(csvAppend(val,nVal));}
        }
        else if($(this).hasClass("ESch")) {
            if(!$("#eus").prop("checked")){
                $("#eus").prop("checked",true);
                $("[for=eus]").addClass("active");
                $("#usr").val(nVal);
            }
            else if(val === ""){$("#usr").val(nVal);}
            else {$("#usr").val(csvAppend(val,nVal));}
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
            window.open($dynmapURL+"?worldname="+nVal[3]+"&mapname="+$dynmapMapName+"&zoom="+$dynmapZoom+"&x="+nVal[0]+"&y="+nVal[1]+"&z="+nVal[2],"CoLWI-dmap");
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
            else {$("#blk").val(csvAppend(val,nVal));}
        }
        else if($(this).hasClass("ESch")) {
            if(!$("#ebl").prop("checked")){
                $("#ebl").prop("checked",true);
                $("[for=ebl]").addClass("active");
                $("#blk").val(nVal);
            }
            else if(val === ""){$("#blk").val(nVal);}
            else {$("#blk").val(csvAppend(val,nVal));}
        }
    }
});
$("#purgeCache").click(function() {
    if(confirm("Do you want to purge the cache? You won't lose any permanent data.")) {
        $.ajax("purge.php",{
          dataType:"json",
          success:function(data){
              if(data[0]) {alert("The ./cache/ directory was purged successfully.");}
              else {alert("Purge was unsuccessful. Please check your settings.");}
          }
        });
    }
});
}
formHandler();