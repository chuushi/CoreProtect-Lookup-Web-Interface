<?php
// WebTemplate
// Constructor: @array Configuration[, @string PageTitle]
// (c) SimonOrJ, 2016

class WebTemplate {
    private $c, $t;
    
    public function __construct(&$config, $title = "CorePortect Lookup Web Interface &bull; by SimonOrJ") {
        $this->c = &$config;
        $this->t = $title;
    }
    // Head
    public function head() {?>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?php echo $this->t;?></title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap-glyphicons.css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.3/css/bootstrap.min.css" integrity="sha384-MIwDKRSSImVFAZCVLtU0LMDdON6KVCrZHyVQQj6e8wIEJkW4tvwqXrbMIya1vriY" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/css/bootstrap-datetimepicker.min.css">
  <link rel="stylesheet" href="res/css/main.css">
  <link rel="stylesheet" href="res/css/jquery-autocomplete.css">
</head>

<?php
    }
    // Navigation Bar
    public function navbar($shownavs = true) {?>
<nav id="top" class="navbar navbar-light bg-faded navbar-full">
  <div class="container">
    <a class="navbar-brand" href="./">CoreProtect Lookup Web Interface</span>
    <ul class="nav navbar-nav">
	  <?php if ($shownavs) foreach($this->c['navbar'] as $ll => $hf) echo '<li class="nav-item"><a class="nav-link" href="'.$hf.'">'.$ll.'</a></li>';?>
    </ul>
    <?php
//if ("<a href="./?action=clear_login" class="btn btn-outline-info pull-xs-right">"
if ($this->c['login']['required']) echo'<a href="./login.php?action=logout" class="btn btn-outline-info pull-xs-right">logout</a>';

    ?>
  </div>
</nav>
<?php
    }
}
?>