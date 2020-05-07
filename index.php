<?php
/**
 * index.php file
 * by Simon Chuu
 */
$config = require "config.php";
?><!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>CoreProtect Lookup Web Interface</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
<!--    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/css/bootstrap-datetimepicker.min.css">-->
<!--    <link rel="stylesheet" href="res/css/main.css">-->
<!--    <link rel="stylesheet" href="res/css/jquery-autocomplete.css">-->
  </head>
  <body data-spy="scroll" data-target="#row-pages">
    <nav id="top" class="navbar navbar-expand-lg navbar-light bg-light mb-3">
      <div class="container">
        <a class="navbar-brand" href="./">CoreProtect Lookup Web Interface</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="nav navbar-nav">
              <?php foreach($config['navbar'] as $link => $href) echo
                  '<li class="nav-item"><a class="nav-link" href="' . $href . '">' . $link . '</a></li>';
              ?>
          </ul>
        </div>
      </div>
    </nav>

<!--    <nav id="scroll-nav" class="navbar navbar-dark bg-inverse navbar-fixed-bottom">
      <div class="container-fluid">
        <ul id="row-pages" class="nav navbar-nav">
          <li class="nav-item"><a class="nav-link" href="#top">Top</a></li>
        </ul>
      </div>
    </nav>
-->
    <div class="container">
<!--      <div class="alert alert-info alert-dismissible fade in" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <strong>Info:</strong> Alert box.  Please consult your administrator.
      </div>
-->
      <!-- Lookup Form -->
      <div id="lookup-form" class="card">
        <div class="card-header">Make a query</div>
        <form class="card-body">

          <div class="form-group input-group">
            <div class="input-group-prepend">
              <label for="lookup-database" class="input-group-text">Server</label>
            </div>
            <select class="custom-select" id="lookup-database">
              <option value="1" selected><?php echo "Server" // TODO ?></option>
            </select>
          </div>

          <div class="form-group input-group">
            <div class="input-group-prepend">
              <span class="input-group-text">Actions</span>
            </div>
            <div class="input-group-append btn-group btn-group-toggle" data-toggle="buttons">
              <label for="form-a-block-add" class="btn btn-outline-secondary">
                <input type="checkbox" id="form-a-block-add"> +Block
              </label>
              <label for="form-a-block-sub" class="btn btn-outline-secondary">
                <input type="checkbox" id="form-a-block-sub"> -Block
              </label>
              <label for="form-a-container-add" class="btn btn-outline-secondary">
                <input type="checkbox" id="form-a-container-add"> +Container
              </label>
              <label for="form-a-container-sub" class="btn btn-outline-secondary">
                <input type="checkbox" id="form-a-container-sub"> -Container
              </label>
              <label for="form-a-chat" class="btn btn-outline-secondary">
                <input type="checkbox" id="form-a-chat"> Chat
              </label>
              <label for="form-a-command" class="btn btn-outline-secondary">
                <input type="checkbox" id="form-a-command"> Command
              </label>
              <label for="form-a-kill" class="btn btn-outline-secondary">
                <input type="checkbox" id="form-a-kill"> Kill
              </label>
              <label for="form-a-session" class="btn btn-outline-secondary">
                <input type="checkbox" id="form-a-session"> Session
              </label>
              <label for="form-a-username" class="btn btn-outline-secondary">
                <input type="checkbox" id="form-a-username"> Username
              </label>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6 col-12 form-group input-group">
              <div class="input-group-prepend">
                <label for="lookup-coords-x" class="input-group-text">Corner 1</label>
              </div>
              <input type="number" class="form-control" id="lookup-coords-x" placeholder="x">
              <input type="number" class="form-control" id="lookup-coords-y" placeholder="y">
              <input type="number" class="form-control" id="lookup-coords-z" placeholder="z">
            </div>
            <div class="col-md-6 col-12 form-group input-group">
              <div class="input-group-prepend">
                <label for="lookup-coords2-x" class="input-group-text">Corner 2</label>
              </div>
              <input type="number" class="form-control" id="lookup-coords2-x" placeholder="x">
              <input type="number" class="form-control" id="lookup-coords2-y" placeholder="y">
              <input type="number" class="form-control" id="lookup-coords2-z" placeholder="z">
            </div>
          </div>

          <div class="form-group input-group">
            <div class="input-group-prepend">
              <label for="lookup-user" class="input-group-text">Users</label>
            </div>
            <input type="text" class="form-control" id="lookup-user" placeholder="Users, comma separated">
            <div class="input-group-append btn-group-toggle" data-toggle="buttons">
              <label for="form-user-exclude" class="btn btn-outline-secondary">
                <input type="checkbox" id="form-user-exclude"> Exclude
              </label>
            </div>
          </div>

          <div class="form-group input-group">
            <div class="input-group-prepend">
              <label for="lookup-material" class="input-group-text">Materials</label>
            </div>
            <input type="text" class="form-control" id="lookup-material" placeholder="Blocks or items, comma separated">
            <div class="input-group-append btn-group-toggle" data-toggle="buttons">
              <label for="form-material-exclude" class="btn btn-outline-secondary">
                <input type="checkbox" id="form-material-exclude"> Exclude
              </label>
            </div>
          </div>

          <div class="form-group input-group">
            <div class="input-group-prepend">
              <label for="lookup-entity" class="input-group-text">Entities</label>
            </div>
            <input type="text" class="form-control" id="lookup-entity" placeholder="Entities, comma separated">
            <div class="input-group-append btn-group-toggle" data-toggle="buttons">
              <label for="form-entity-exclude" class="btn btn-outline-secondary">
                <input type="checkbox" id="form-entity-exclude"> Exclude
              </label>
            </div>
          </div>

          <div class="form-group input-group">
            <div class="input-group-prepend">
              <label for="lookup-keyword" class="input-group-text">Keyword</label>
            </div>
            <input type="text" class="form-control" id="lookup-keyword" placeholder="Keywords (roughly implemented)">
          </div>

          <div class="row">
            <div class="col-md-6 col-12 form-group input-group">
              <div class="input-group-prepend">
                <label for="lookup-time" class="input-group-text">Date/Time</label>
              </div>
              <!-- TODO: Javascript flop to type="number" -->
              <input type="datetime-local" class="form-control" id="lookup-time" placeholder="0000-00-00T00:00:00">
            </div>
            <div class="col-md-6 col-12 form-group input-group">
              <div class="input-group-prepend">
                <label for="lookup-limit" class="input-group-text">Limit</label>
              </div>
              <input type="number" class="form-control" id="lookup-limit" min="1" max="<?php echo $config['form']['max'];?>" placeholder="<?php echo $config['form']['limit'];?>">
            </div>
          </div>

          <input class="btn btn-secondary" type="submit" id="lookup-submit" value="Make a Lookup">
        </form>
      </div>
    </div>

    <!-- Output table -->
    <div class="container-fluid">
      <table id="mainTable" class="table table-sm table-striped">
        <thead class="thead-inverse">
          <tr id="row-0">
            <th>#</th>
            <th>Time</th>
            <th>User</th>
            <th>Action</th>
            <th>Coordinates/World</th>
            <th>Entity/Block/Item[Data]</th>
            <th>Amount</th>
            <th>Rollback</th>
          </tr>
        </thead>
        <tbody id="outputTable">
          <tr>
            <th scope="row">-</th>
            <td colspan="7">Please submit a lookup.</td>
          </tr>
        </tbody>
        <caption id="queryTime"></caption>
      </table>
    </div>

    <div class="container">

      <!-- Load More form -->
      <div id="lookup-form" class="card">
        <form class="card-body">
          <input id="more-offset" type="hidden" value="0">

          <div class="row">
            <div class="col-md-6 col-12 form-group input-group">
              <div class="input-group-prepend">
                <label for="more-limit" class="input-group-text">Load next</label>
              </div>
              <input type="number" class="form-control" id="more-limit" min="1" max="<?php echo $config['form']['max'];?>" placeholder="<?php echo $config['form']['moreLimit'];?>">
            </div>
          </div>

          <input class="btn btn-secondary" type="submit" id="more-submit" value="Load more">
        </form>
      </div>


      <p>If you encounter any issues, please open an issue on the <a href="https://github.com/SimonOrJ/CoreProtect-Lookup-Web-Interface">GitHub project page</a>.</p>
    </div>

    <!-- Copyright Message -->
    <div class="container-fluid">
      <p>&copy; <?php echo str_replace("%year%", date("Y"),$config["copyright"]);?>. <span class="">CoreProtect LWI v0.9.3-beta &ndash; Created by <a href="http://simonorj.com/">Simon Chuu</a>.</span></p>
    </div>

    <!-- All the scripting needs -->
    <?php
    // Unset sensetitive information before sending it to the JS.
    unset($config['login']);
    unset($config['user']);
    ?>
<!--    <script>-->
<!--    // Quick Styling for JS-enabled browser-->
<!--    -->
<!--    // Corner/Radius Reset-->
<!--    document.getElementById("lCorner1").innerHTML = "Center";-->
<!--    document.getElementById("lCorner2").innerHTML = "Radius";-->
<!--    document.getElementById("lC2").className = "";-->
<!--    a = document.getElementsByClassName("lRadiusHide");-->
<!--    for (var i = 0; i < a.length; i++) a[i].style.display = "none";-->
<!--    document.getElementById("lCX2").setAttribute("placeholder","Radius");-->
<!--    -->
<!--    // Add data-toggle attribute to checkboxes (and radio buttons) with dtButtons php-->
<!--    a = document.getElementsByClassName("dtButtons");-->
<!--    for (var i = 0; i < a.length; i++) a[i].setAttribute("data-toggle","buttons");-->
<!--    document.getElementById("lT").setAttribute("placeholder","")-->
<!--    document.getElementById("lT").setAttribute("type","text");-->
<!--    document.getElementById("lT").removeAttribute("name");-->
<!--    -->
<!--    document.getElementById("lSubmit").disabled = true;-->
<!--    document.getElementById("mSubmit").disabled = true;-->
<!--    </script>-->
<!--    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js">// JQuery</script>-->
<!--    <script src="res/js/buttons.js"></script>-->
<!--    <script src="https://code.jquery.com/ui/1.11.4/jquery-ui.js">// Dropdown</script>-->
<!--    <script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.1.1/js/tether.min.js">// Bootstrap dependency</script>-->
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
<!--    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.14.1/moment-with-locales.min.js">// datetime-picker dependency</script>-->
<!--    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js">// Datetime Picker</script>-->
<!--    <script src="res/js/lookup.js"></script>-->
  </body>
</html>
