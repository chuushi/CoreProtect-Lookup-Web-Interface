(function () {
"use strict";
/*
 * Lookup class
 */
// action constants
const A_BLOCK_MINE    = 0x0001;
const A_BLOCK_PLACE   = 0x0002;
const A_CLICK         = 0x0004;
const A_KILL          = 0x0008;
const A_CONTAINER_OUT = 0x0010;
const A_CONTAINER_IN  = 0x0020;
const A_CHAT          = 0x0040;
const A_COMMAND       = 0x0080;
const A_SESSION       = 0x0100;
const A_USERNAME      = 0x0200;

const A_BLOCK_MATERIAL = A_BLOCK_MINE | A_BLOCK_PLACE | A_CLICK;
const A_BLOCK_TABLE = A_BLOCK_MATERIAL | A_KILL;
const A_CONTAINER_TABLE = A_CONTAINER_IN | A_CONTAINER_OUT;
const A_LOOKUP_TABLE = A_BLOCK_TABLE | A_CONTAINER_TABLE | A_CHAT | A_COMMAND | A_SESSION | A_USERNAME;

const A_EX_USER       = 0x0400;
const A_EX_BLOCK      = 0x0800;
const A_EX_ENTITY     = 0x1000;
const A_EX_WORLD      = 0x2000;
const A_ROLLBACK_YES  = 0x4000;
const A_ROLLBACK_NO   = 0x8000;
const A_REV_TIME      = 0x10000;

// Commonly encountered DOM references in an object
const $lookup = {
    form: $("#lookup-form"),
    server: $("#lookup-database"),
    actionBlockAdd: $("#lookup-a-block-add"),
    actionBlockSub: $("#lookup-a-block-sub"),
    actionContainerAdd: $("#lookup-a-container-add"),
    actionContainerSub: $("#lookup-a-container-sub"),
    actionKill: $("#lookup-a-kill"),
    actionClick: $("#lookup-a-click"),
    actionChat: $("#lookup-a-chat"),
    actionCommand: $("#lookup-a-command"),
    actionSession: $("#lookup-a-session"),
    actionUsername: $("#lookup-a-username"),
    rollbackYes: $("#lookup-rollback-yes"),
    rollbackNo: $("#lookup-rollback-no"),
    x1: $("#lookup-coords-x"),
    y1: $("#lookup-coords-y"),
    z1: $("#lookup-coords-z"),
    x2: $("#lookup-coords2-x"),
    y2: $("#lookup-coords2-y"),
    z2: $("#lookup-coords2-z"),
    world: $("#lookup-world"),
    user: $("#lookup-user"),
    material: $("#lookup-material"),
    entity: $("#lookup-entity"),
    keyword: $("#lookup-keyword"),
    time: $("#lookup-time"),
    worldEx: $("#lookup-world-exclude"),
    userEx: $("#lookup-user-exclude"),
    materialEx: $("#lookup-material-exclude"),
    entityEx: $("#lookup-entity-exclude"),
    timeRev: $("#lookup-time-rev"),
    limit: $("#lookup-limit"),
    submit: $("#lookup-submit")
};

const $more = {
    form: $("#more-form"),
    limit: $("#more-limit"),
    submit: $("#more-submit")
};

const $table = $("#output-table");
const $tableBody = $("#output-body");
const $queryTime = $("#output-time");
const $pages = $("#row-pages");

let config = {form: {count: 30, moreCount: 10}}; // TODO
let currentLookup = null, currentCount = 0, ajaxWaiting = false;

$lookup.form.submit(function (ev) {
    submit(ev, false);
});

$more.form.submit(function (ev) {
    submit(ev, true);
});

function submit(ev, more) {
    ev.preventDefault();
    if (ajaxWaiting) {
        // TODO message
        console.log("Waiting on ajaxWaiting to flip");
        return;
    }

    if (more) serializeMore();
    else serializeLookup();
    console.log(currentLookup);

    if (currentLookup == null) {
        // TODO "An action is required"
        console.log("An action is required");
        return;
    }

    console.log(currentLookup);

    $.ajax("lookup.php", {
        method: "POST",
        data: currentLookup,
        dataType: "json",
        beforeSend: beforeSend,
        success: more ? moreSuccess : lookupSuccess,
        error: more ? moreError : lookupError,
        complete: complete
    });
}

function serializeLookup() {
    let a = 0;

    // Serialize Action/a variable
    if ($lookup.actionBlockAdd.prop("checked")) a |= A_BLOCK_PLACE;
    if ($lookup.actionBlockSub.prop("checked")) a |= A_BLOCK_MINE;
    if ($lookup.actionContainerAdd.prop("checked")) a |= A_CONTAINER_IN;
    if ($lookup.actionContainerSub.prop("checked")) a |= A_CONTAINER_OUT;
    if ($lookup.actionKill.prop("checked")) a |= A_KILL;
    if ($lookup.actionClick.prop("checked")) a |= A_CLICK;
    if ($lookup.actionChat.prop("checked")) a |= A_CHAT;
    if ($lookup.actionCommand.prop("checked")) a |= A_COMMAND;
    if ($lookup.actionSession.prop("checked")) a |= A_SESSION;
    if ($lookup.actionUsername.prop("checked")) a |= A_USERNAME;
    if ($lookup.rollbackYes.prop("checked")) a |= A_ROLLBACK_YES;
    if ($lookup.rollbackNo.prop("checked")) a |= A_ROLLBACK_NO;
    if ($lookup.worldEx.prop("checked")) a |= A_EX_WORLD;
    if ($lookup.userEx.prop("checked")) a |= A_EX_USER;
    if ($lookup.materialEx.prop("checked")) a |= A_EX_BLOCK;
    if ($lookup.entityEx.prop("checked")) a |= A_EX_ENTITY;
    if ($lookup.timeRev.prop("checked")) a |= A_REV_TIME;

    if ((a & A_LOOKUP_TABLE) === 0)
        return;

    currentCount = 0;
    currentLookup = {a: a};

    let form = $lookup.form.serializeArray();
    for (let i = 0; i < form.length; i++) {
        if (form[i].value !== "")
            currentLookup[form[i].name] = form[i].value;
    }

    // currentLookup.t = $lookup.time.val(); // TODO time calculation
}

function serializeMore() {
    if (currentLookup === null)
        return;

    let count = Number.parseInt($more.limit.val());
    currentLookup.offset = currentCount;
    if (isNaN(count)) delete (currentLookup.count);
    else currentLookup.count = count;
}

function beforeSend(xhr, settings) {
    ajaxWaiting = true;
    $lookup.submit.prop("disabled", true);
    $more.submit.prop("disabled", true);
}

function complete(xhr, status) {
    ajaxWaiting = false;
    $lookup.submit.prop("disabled", false);
    $more.submit.prop("disabled", false);
}

function lookupSuccess(data, status, xhr) {
    // TODO Populate table
    populateTable(data, false);
}

function lookupError(xhr, status, thrown) {
    // TODO Show error
}

function moreSuccess(data, status, xhr) {
    // TODO Expand table
    populateTable(data, true);
}

function moreError(xhr, status, thrown) {
    // TODO Show error
}

function populateTable(data, append) {
    let html = [];

    $queryTime.text("Request generated in "+Math.round(data[0].duration*1000)+"ms");

    if (data[0].status !== 0) {
        // TODO show error
        $tableBody.append('<tr><th></th><td colspan="6">Error</td></tr>'); // TODO: icon
        return;
    }

    const rows = data[1];

    if (rows.length === 0) { // TODO: in php file, send an empty array on no length.
        // TODO if descending order, leave it open. otherwise,
        $tableBody.append('<tr><th></th><td colspan="6">No more results</td></tr>'); // TODO: icon
        return;
    }

    if (!append)
        $tableBody.empty();

    for (let i = 0; i < rows.length; i++) {
        currentCount++;
        $tableBody.append(populateRow(rows[i]));
    }
}

function populateRow(row) {
    let stuff;

    let testrow = {
        "rowid":"36782",
        "table":"block",
        "time":"1560540351",
        "user":"SimonOrJ",
        "uuid":"39d83509-f85f-492a-ba8d-f54ad74c2682",
        "action":"1",
        "world":"world",
        "x":"187",
        "y":"65",
        "z":"-198",
        "target":"minecraft:bell",
        "data":"0",
        "amount":null,
        "rolled_back":"0"
    };

    stuff = `<td>${row.time}</td><td>${row.user}</td><td>${row.action}</td><td>${
        row.world + ' ' + row.x + ' ' + row.y + ' ' + row.z
    }</td><td>${row.target}</td><td>${row.rolled_back}</td>`;

    switch (row.table) {
        case "click":
        case "session":
            // Not rollbackable
        case "container":
        case "block":
        case "kill":
            // coordinates
            break;
        case "chat":
        case "command":
        case "username_log":
            // Message/UUID
            break;
    }

    return `<tr><th>${currentCount}</th>${stuff}</tr>`;
}

const csv = {
    append: function (text, value) {
        return $.inArray(value, text.split(/, ?/)) === -1 ? text + ", " + value : text;
    },
    array: function (value) {
        return value.split(/, ?/);
    },
    join: function (array) {
        return array.join(", ");
    }
};

// There are no tooltips yet
//$('[data-toggle="tooltip"]').tooltip();
}());