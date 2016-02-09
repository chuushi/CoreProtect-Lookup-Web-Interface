/* CoreProtect LWI - v0.7.1-beta
 * Javascript code by SimonOrJ.
 * this uses jQuery and jQuery UI.
 */
function formHandler() {
"use strict";
$("#date").datetimepicker({format:$dateFormat+" "+$timeFormat});
$("[for=abl]").addClass("active");

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

// usr, blk, wid
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
}
formHandler();