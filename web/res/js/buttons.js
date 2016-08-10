(function(){
"use strict";

$('.dtButtons label').each(function(i, el) {
    var e = $(el);
    if (e.children()[0].hasAttribute("checked")) {
        e.addClass("active");
    }
    // Ohhhhh, so this is how it works! :D
});

}());