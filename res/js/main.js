/**
 * Main JavaScript
 *
 * CoreProtect Lookup Web Interface
 * @author Simon Chuu
 * @copyright 2015-2020 Simon Chuu
 * @license MIT License
 * @link https://github.com/chuushi/CoreProtect-Lookup-Web-Interface
 * @since 1.0.0
 */
(function () {
"use strict";

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
    r: $("#lookup-coords-radius"),
    coordsLabel: $("#lookup-coords-label"),
    coordsToggle: $("#lookup-coords-toggle"),
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
    submit: $("#lookup-submit"),
    alert: $("#lookup-alert")
};

const $more = {
    form: $("#more-form"),
    limit: $("#more-limit"),
    submit: $("#more-submit"),
    alert: $("#more-alert")
};

const $tableBody = $("#output-body");
const $queryTime = $("#output-time");
const $pages = $("#row-pages");

const $login = {
    name: $("#login-name"),
    activate: $("#login-activate"),
    modal: $("#login-modal"),
    form: $("#login-form"),
    username: $("#login-username"),
    password: $("#login-password"),
    remember: $("#login-remember"),
    submit: $("#login-submit"),
    alert: $("#login-alert")
};

// Configuration constants
const dateTimeFormat = config.dateTimeFormat;

moment.defaultFormat = dateTimeFormat;
$lookup.time.datetimepicker({
    format: dateTimeFormat,
    // https://stackoverflow.com/questions/47618134/bootstrap-datetimepicker-for-bootstrap-4
    icons: {
        time: 'fa fa-clock-o',
        date: 'fa fa-calendar',
        up: 'fa fa-chevron-up',
        down: 'fa fa-chevron-down',
        previous: 'fa fa-chevron-left',
        next: 'fa fa-chevron-right',
        today: 'fa fa-check',
        clear: 'fa fa-trash',
        close: 'fa fa-times'
    }
});


// ##########################
//  Corner and Radius Toggle
// ##########################
const CORNER = "Corner";
const CENTER = "Center";
const RADIUS = "Radius";
$lookup.coordsToggle.click(function () {coordsToggle()});

function coordsToggle(center) {
    const $r = $lookup.r;
    const isCenter = $r.prop("hidden");
    if (center !== false && isCenter) {
        $lookup.r.prop("hidden", false);

        $lookup.coordsLabel.text(CENTER);
        $lookup.coordsToggle.text(RADIUS);

        $lookup.x2.prop("hidden", true);
        $lookup.y2.prop("hidden", true);
        $lookup.z2.prop("hidden", true);
        $lookup.x2.prop("disabled", true);
        $lookup.y2.prop("disabled", true);
        $lookup.z2.prop("disabled", true);
    } else if (center !== true && !isCenter) {
        $lookup.x2.prop("disabled", false);
        $lookup.y2.prop("disabled", false);
        $lookup.z2.prop("disabled", false);
        $lookup.x2.prop("hidden", false);
        $lookup.y2.prop("hidden", false);
        $lookup.z2.prop("hidden", false);

        $lookup.coordsLabel.text(CORNER + ' ' + 1);
        $lookup.coordsToggle.text(CORNER + ' ' + 2);

        $lookup.r.prop("hidden", true);
        $lookup.r.val("");
    }
}


// ##################
//  Date/Time parser
// ##################
// All times are baed on UNIX timestamp (seconds since)
function unixToString(unix) {
    return moment.unix(unix).format(dateTimeFormat);
}

function setLookupTime(unix, reverse) {
    $lookup.time.val(unixToString(unix));
    const checked = $lookup.timeRev.prop("checked");
    if (typeof reverse === "boolean" && reverse !== checked) {
        $lookup.timeRev.prop("checked", reverse);
        $lookup.timeRev.parent().button("toggle", reverse);
    }
}

function getLookupTime() {
    return moment($lookup.time.val(), dateTimeFormat).unix();
}


// ###################
//  Login form parser
// ###################
$login.form.submit(function (ev) {
    ev.preventDefault();

    const credentials = {
        username: $login.username.val(),
        password: $login.password.val()
    };

    if ($login.remember.prop('checked'))
        credentials.remember = true;

    $.ajax("login.php", {
        method: "POST",
        data: credentials,
        dataType: "json",
        beforeSend: function () {
            $login.submit.prop('disabled', true);
        },
        success: function (data) {
            if (data.success) {
                $login.modal.modal('hide');
                login(data.username);
                $login.username.val('');
                $login.password.val('');
            } else {
                $login.alert.html(getAlertElement('Wrong credentials', "warning"));
            }
        },
        error: function (xhr, status, thrown) {
            let text = "";

            if (thrown)
                text = thrown;
            else if (xhr.status === 0)
                text = "Timed out (Check your internet connection)";
            else
                text = xhr.status + "";

            $login.alert.html(getAlertElement(text, "danger"));
        },
        complete: function () {
            $login.submit.prop('disabled', false);
        }
    });
});

$login.activate.click(function (ev) {
    if (loginUsername === null)
        return;

    $.ajax("login.php", {
        method: "POST",
        data: {logout: true},
        dataType: "json",
        beforeSend: function () {
            $login.submit.prop('disabled', true);
        },
        success: function (data) {
            if (data.success) {
                $login.alert.html(getAlertElement("Logged out successfully", "success"));
                logout();
            } else { // This shouldn't happen
                $login.alert.html(getAlertElement("???", "warning"));
            }
        },
        error: function (xhr, status, thrown) {
            let text;

            if (thrown)
                text = thrown;
            else if (xhr.status === 0)
                text = "Timed out (Check your internet connection)";
            else
                text = xhr.status + "";

            $login.alert.html(getAlertElement(text, "danger"));
        },
        complete: function () {
            $login.submit.prop('disabled', false);
        }
    });
});

function login(username) {
    if (username === null)
        return;

    loginUsername = username;

    $login.name.html(`Hello, <b>${username}</b>!`);
    $login.activate.text("Logout");
}

function logout() {
    if (loginUsername === null)
        return;

    loginUsername = null;

    $login.name.html("");
    $login.activate.text("Login");
}

// ####################
//  Lookup form parser
// ####################
let currentLookup = null;
let currentCount = 0;
let ajaxWaiting = false;
let queryFlags = null;

$lookup.form.submit(function (ev) {
    submit(ev, false);
});

$more.form.submit(function (ev) {
    submit(ev, true);
});

function submit(ev, more) {
    ev.preventDefault();
    if (ajaxWaiting) {
        addAlert("Please wait for the previous lookup to complete.", more, "info");
        return;
    }

    if (more) {
        if (currentLookup == null) {
            addAlert("A lookup is required.", true, "info");
            return;
        }
        serializeMore();
    } else {
        const a = serializeActions();
        if (!a) {
            addAlert("An action is required.", false, "info");
            return;
        }
        serializeLookup(a);
    }

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

function serializeActions() {
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
        return 0;
    return a;
}

function serializeLookup(actions) {
    if (!actions)
        return;

    currentCount = 0;
    currentLookup = {a: actions};

    let form = $lookup.form.serializeArray();
    for (let i = 0; i < form.length; i++) {
        if (form[i].value !== "")
            currentLookup[form[i].name] = form[i].value;
    }

    delete(form.rollback);

    const rs = $lookup.r.val();
    if (rs !== "") {
        const xs = $lookup.x1.val();
        const ys = $lookup.y1.val();
        const zs = $lookup.z1.val();

        if (xs !== "" && ys !== "" && zs !== "") {
            const r = parseInt(rs);
            const x = parseInt(xs);
            const y = parseInt(ys);
            const z = parseInt(zs);
            currentLookup.x = x - r;
            currentLookup.y = y - r;
            currentLookup.z = z - r;
            currentLookup.x2 = x + r;
            currentLookup.y2 = y + r;
            currentLookup.z2 = z + r;
        }
    }

    const time = $lookup.time.val();
    if (time !== "") {
        currentLookup.t = getLookupTime();
    }

    if (queryFlags !== null) {
        currentLookup.flags = queryFlags;
    }
}

function serializeMore() {
    if (currentLookup === null)
        return;

    let count = Number.parseInt($more.limit.val());
    currentLookup.offset = currentCount;
    if (isNaN(count)) delete (currentLookup.count);
    else currentLookup.count = count;
}

function beforeSend() {
    ajaxWaiting = true;
    $lookup.submit.prop("disabled", true);
    $more.submit.prop("disabled", true);
}

function complete() {
    ajaxWaiting = false;
    $lookup.submit.prop("disabled", false);
}

function lookupSuccess(data) {
    populateTable(data, false);
}

function lookupError(xhr, status, thrown) {
    xhrError(xhr, status, thrown, false);
}

function moreSuccess(data) {
    populateTable(data, true);
}

function moreError(xhr, status, thrown) {
    xhrError(xhr, status, thrown, true);
}


// ####################
//  Result Parser
// ####################
let mapHref;

function xhrError(xhr, status, thrown, more) {
    let text;
    if (status === "parsererror") {
        text = thrown + " (Possible webserver PHP misconfiguration)";
    } else if (thrown) {
        text = xhr.status + ": " + thrown;
        if (xhr.status === 401)
            text += ' (You\'re not logged in. <a href="#" data-toggle="modal" data-target="#login-modal">Login</a>)';
    } else if (xhr.status === 0) {
        text = "Timed out (Check your internet connection)";
    } else {
        text = xhr.status + "";
    }
    addAlert(text, more, "danger");
}

function populateTable(data, more) {
    $queryTime.text("Request generated in "+Math.round(data[0].duration*1000)+"ms");

    if (data[0].status !== 0) {
        let st = data[0];
        let text;
        if (st.status === 1) {
            text = `${st.code}: ${st.reason}`
        } else if (st.status === 2) {
            text = `${st.code} (${st.driverCode}): ${st.reason}`
        } else {
            text = "Unknown error occured."
        }
        addAlert(text, more, "danger");
        return;
    }

    const rows = data[1];

    if (rows.length === 0) {
        if (more) {
            if ((currentLookup.a & A_REV_TIME ) === 0)
                $tableBody.prepend('<tr><th><i class="fa fa-minus"></i></th><td colspan="5">No more results</td></tr>');
            else {
                addAlert("No more results. (If on a live server, wait a bit then submit for more results)", more, "info");
                $more.submit.prop("disabled", false);
            }
        } else {
            addAlert("That lookup returned no results.", more, "info");
        }
        return;
    }

    if (data[0].mapHref)
        mapHref = data[0].mapHref;

    if (queryFlags === null) {
        queryFlags = currentLookup.flags = data[0].flags;
    }

    if (!more) {
        $tableBody.empty();
        if (!currentLookup.t)
            currentLookup.t = data[1][0].time;
    }

    for (let i = 0; i < rows.length; i++) {
        currentCount++;
        $tableBody.append(populateRow(rows[i]));
    }

    // Allow submitting more lookups
    $more.submit.prop("disabled", false);
}

function addAlert(text, more, level) {
    if (!level)
        level = "warning";
    const $alert = more ? $more.alert : $lookup.alert;

    $alert.prepend(getAlertElement(text, level));
}

function populateRow(row) {
    let userAttr = {type: "user", user: row.user};
    if (row.uuid) userAttr.uuid = row.uuid;
    const ret = document.createElement("tr");

    const rowEl = document.createElement("th");
    rowEl.title = "Row ID: " + row.id;
    rowEl.innerText = currentCount;
    ret.append(rowEl);

    const dateEl = document.createElement("td");
    dateEl.classList.add("dropdown");
    dateEl.innerText = unixToString(row.time) + " ";
    dateEl.append(addDropButton({type: "date", time: row.time}));
    ret.append(dateEl);

    const userEl = document.createElement("td");
    userEl.classList.add("dropdown");
    userEl.innerText = row.user + " ";
    userEl.append(addDropButton({type: "user", user: row.user, uuid: row.uuid}));
    ret.append(userEl);

    switch (row.table) {
        case "session":
        case "container":
        case "block":
            let actionInner, badgeStyle, dataType;
            let rollback = row.rolled_back ? '</span> <span class="badge badge-light">Rolled Back' : "";
            let amount = row.amount !== null ? `</span> <span class="badge badge-secondary">${row.amount}` : "";

            switch (row.action) {
                case 0:
                    actionInner = `-${row.table + amount + rollback}`;
                    badgeStyle = "danger";
                    dataType = "material";
                    break;
                case 1:
                    actionInner = `+${row.table + amount + rollback}`;
                    badgeStyle = "success";
                    dataType = "material";
                    break;
                case 2:
                    actionInner = "click";
                    badgeStyle = "info";
                    dataType = "material";
                    break;
                case 3:
                    actionInner = "kill" + rollback;
                    badgeStyle = "warning";
                    dataType = "entity";
                    break;
                default:
                    actionInner = `${row.table + amount + rollback}`;
                    badgeStyle = "info";
                    dataType = row.table;
            }

            const actionEl = document.createElement("td");
            actionEl.innerHTML = `<span class="badge badge-${badgeStyle}">${actionInner}</span>`;
            ret.append(actionEl);

            const coordsEl = document.createElement("td");
            coordsEl.classList.add("dropdown");
            coordsEl.innerText = row.x + ' ' + row.y + ' ' + row.z + ' ' + row.world + ' ';
            coordsEl.append(addDropButton({type: "coordinates", world: row.world, x: row.x, y: row.y, z: row.z}));
            ret.append(coordsEl);

            const targetEl = document.createElement("td");
            if (row.table !== "session") {
                let targetAttr = {type: dataType, item: row.target};
                let targetInner = row.target;
                if (row.data !== null && row.data !== "0") {
                    if (dataType === "material")
                        targetInner += "[" + row.data + "]";
                    else
                        targetAttr.data = row.data;
                }

                targetEl.innerText = targetInner + " ";
                targetEl.classList.add("dropdown");
                targetEl.append(addDropButton(targetAttr));
            }
            ret.append(targetEl);

            break;
        case "chat":
        case "command":
        case "username":
            const action2El = document.createElement("td");
            action2El.innerHTML = `<span class="badge badge-info">${row.table}</span></td>`;
            ret.append(action2El);
            const target2El = document.createElement("td");
            target2El.colSpan = 2;
            target2El.innerHTML = row.target;
            ret.append(target2El);
            break;
    }

    return ret;
}

function addDropButton(attrMap) {
    const ret = document.createElement("button");
    ret.classList.add("btn", "btn-secondary", "btn-inline", "output-add-dropdown", "dropdown-toggle", "dropdown-toggle-split");
    ret.role = "button";
    for (const prop in attrMap)
        // noinspection JSUnfilteredForInLoop
        ret.dataset[prop] = attrMap[prop];
    ret.dataset.toggle = "dropdown";
    return ret;
}

function makeMapHref(dataset) {
    return mapHref
        .replace("{world}", dataset.world)
        .replace("{x}", dataset.x)
        .replace("{y}", dataset.y)
        .replace("{z}", dataset.z);
}

// ###################
//  Dropdown Listener
// ###################
const LT1 = "lt1";
const LT2 = "lt2";
const LT3 = "lt3";
const frameName = "co_map";

$tableBody.on("click", ".output-add-dropdown", function() {
    const addon = document.createElement("div");
    addon.classList.add("dropdown-menu");
    const lt1 = document.createElement("a");
    const lt2 = document.createElement("a");
    lt1.classList.add("dropdown-item");
    lt2.classList.add("dropdown-item");
    lt1.dataset.fillPos = LT1;
    lt2.dataset.fillPos = LT2;
    lt1.href = lt2.href = "#";

    switch (this.dataset.type) {
        case "date":
            const time = document.createElement("span");
            time.classList.add("dropdown-item-text");
            time.innerHTML = `Unix time: <code>${this.dataset.time}</code>`;
            lt1.innerHTML = "Before";
            lt2.innerHTML = "After";
            addon.append(time);
            break;
        case "user":
            const uuid = document.createElement("span");
            uuid.classList.add("dropdown-item-text");
            uuid.innerHTML = `UUID: <code>${this.dataset.uuid}</code>`;
            lt1.innerHTML = "Include";
            lt2.innerHTML = "Exclude";
            break;
        case "coordinates":
            if (mapHref) {
            const map = document.createElement("a");
                map.classList.add("dropdown-item");
                map.href = makeMapHref(this.dataset);
                map.innerHTML = "Open in map";
                map.target = frameName;
                addon.append(map);
            }
            const cntr = document.createElement("a");
            cntr.classList.add("dropdown-item");
            cntr.dataset.fillPos = LT3;
            cntr.href = "#";
            cntr.innerHTML = "Center";
            lt1.innerHTML = "Corner 1";
            lt2.innerHTML = "Corner 2";
            addon.append(cntr);
            break;
        case "material":
            lt1.innerHTML = "Include";
            lt2.innerHTML = "Exclude";
            break;
        case "entity":
            const enid = document.createElement("span");
            if (this.dataset.data) {
                enid.innerHTML = (this.dataset.data.length === 36 ? "UUID" : "Entity row ID") + `: <code>${this.dataset.data}</code>`;
                enid.classList.add("dropdown-item-text");
                addon.append(enid);
            }
            lt1.innerHTML = "Include";
            lt2.innerHTML = "Exclude";
            break;
    }
    addon.append(lt1);
    addon.append(lt2);

    // Prevent dropdown from collapsing when clicked inside
    $(addon).on("click", ":not(.dropdown-item)", function (e) {
        e.stopPropagation();
    });

    // Prevent dropdown from collapsing when clicked inside
    $(addon).on("click", ".dropdown-item", dropdownAutofill);

    this.after(addon);
    this.classList.remove("output-add-dropdown");
    this.classList.add("output-dropdown");
});

function dropdownAutofill(ev) {
    const fillPos = this.dataset.fillPos;
    if (!fillPos)
        return;

    ev.preventDefault();
    const data = this.parentElement.previousSibling.dataset;
    let $elem, $toggle;
    let item;

    switch (data.type) {
        case "user":
            item = data.user;
            $elem = $lookup.user;
            $toggle = $lookup.userEx;
            break;
        case "material":
            item = data.item;
            $elem = $lookup.material;
            $toggle = $lookup.materialEx;
            break;
        case "entity":
            item = data.item;
            $elem = $lookup.entity;
            $toggle = $lookup.entityEx;
            break;
        case "date":
            setLookupTime(data.time, fillPos === LT2);
            return;
        case "coordinates":
            dropdownCoordsAutofill(data, fillPos);
            return;
    }

    console.log(item);
    console.log($elem.val());
    console.log($toggle.prop("checked"));

    const exclude = fillPos === LT2;
    const checked = $toggle.prop("checked");

    if (checked === exclude)
        $elem.val(csvSetAdd($elem.val(), item));
    else {
        const res = csvSetRemove($elem.val(), item);
        if (res) {
            $elem.val(res);
        } else {
            $toggle.prop("checked", !checked);
            $toggle.parent().button("toggle");
            $elem.val(item);
        }
    }
}

function dropdownCoordsAutofill(data, fillPos) {
    if (fillPos === LT2) {
        coordsToggle(false);
        $lookup.x2.val(data.x);
        $lookup.y2.val(data.y);
        $lookup.z2.val(data.z);
    } else {
        $lookup.x1.val(data.x);
        $lookup.y1.val(data.y);
        $lookup.z1.val(data.z);

        if (fillPos === LT1) {
            coordsToggle(false);
        } else {
            coordsToggle(true);
        }
    }

    $lookup.world.val(data.world);
    $lookup.worldEx.prop("checked", false);
    $lookup.worldEx.parent().button("toggle", false);
}

// ###################
//  Utility Functions
// ###################
function csvSetAdd(text, value) {
    return text === "" ? value : text.split(/ *, */).includes(value) ? text : text + ", " + value;
}

function csvSetRemove(text, value) {
    const parts = text.split(/ *, */);
    let i = parts.indexOf(value);
    if (i === -1) {
        return false;
    } else {
        parts.splice(i, 1);
        return parts.join(", ");
    }
}

function getAlertElement(text, level) {
    return `<div class="alert alert-${level} alert-dismissible" role="alert">${text}<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>`;
}

// Run functions
login(loginUsername);
if (loginRequired)
    $login.modal.modal('show');
}());
