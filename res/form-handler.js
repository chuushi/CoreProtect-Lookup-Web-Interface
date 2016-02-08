/* CoreProtect LWI - v0.7.0-beta
 * Javascript code by SimonOrJ.
 * this uses jQuery and jQuery UI.
 */
function formHandler() {
"use strict";
$("#date").datetimepicker({format:$dateFormat+" "+$timeFormat});
$("[for=abl]").addClass("active");

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
    function split( val ) {
      return val.split(/,/);
    }
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
            var a = split(request.term);
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
            var terms = split( this.value );
            terms.pop();
            terms.push( ui.item.value );
            this.value = terms.join( "," );
            return false;
        },
        select: function( event, ui ) {
            var terms = split( this.value );
            terms.pop();
            terms.push( ui.item.value );
            this.value = terms.join( "," );
            return false;
        }
      });
};
formHandler();