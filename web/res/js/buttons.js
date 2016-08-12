// CoLWI v0.9.0
// buttons JavaScript
// Copyright (c) 2015-2016 SimonOrJ

// Function that checks all checkboxes and radio buttons that are checked.
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