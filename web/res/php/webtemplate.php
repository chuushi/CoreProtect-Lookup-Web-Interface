<?php
// WebTemplate
// Constructor: @array Configuration[, @string PageName]
// (c) SimonOrJ, 2016

class WebTemplate {
    private $c, $n;
    
    public function __construct(&$config, $name = "CorePortect Lookup Web Interface &bull; by SimonOrJ") {
        $this->c = &$config;
        $this->n = $name;
    }
    // Head
    public function head() {?>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>CorePortect Lookup Web Interface &bull; by SimonOrJ</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap-glyphicons.css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.3/css/bootstrap.min.css" integrity="sha384-MIwDKRSSImVFAZCVLtU0LMDdON6KVCrZHyVQQj6e8wIEJkW4tvwqXrbMIya1vriY" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/css/bootstrap-datetimepicker.min.css">
  <link rel="stylesheet" href="res/css/main.css">
  <link rel="stylesheet" href="res/css/jquery-autocomplete.css">
</head>

<?php
    }
    // Navigation Bar
    public function navbar() {?>
<nav id="top" class="navbar navbar-light bg-faded navbar-full">
  <div class="container">
    <a class="navbar-brand" href="./">CoreProtect Lookup Web Interface</span>
    <ul class="nav navbar-nav">
	  <?php foreach($this->c['navbar'] as $ll => $hf) echo '<li class="nav-item"><a class="nav-link" href="'.$hf.'">'.$ll.'</a></li>';?>
    </ul>
    <?php if ($this->c['login']['required']) echo'<a href="./?action=clear_login" class="btn btn-outline-info pull-xs-right">logout</a>';?>
  </div>
</nav>
<?php
    }
}
?>