<?php
// in case we forget to define $page->navbar or we just want to use the default values

if($page->navbar == array())
    $page->navbar = $presets->GenerateNavbar();

if(!$user->islg()) // if it's not logged in we hide the user menu
    unset($page->navbar[2]);


?>

<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title><?php echo $page->title; ?></title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width">

        <link rel="stylesheet" href="css/bootstrap.min.css">
        <style>
            body {
                padding-top: 60px;
                padding-bottom: 40px;
            }
        </style>
        <link rel="stylesheet" href="css/bootstrap-responsive.min.css">
        <link rel="stylesheet" href="css/main.css">

        <script src="js/vendor/modernizr-2.6.2-respond-1.1.0.min.js"></script>
        

    </head>
    <body>
        <!--[if lt IE 7]>
            <p class="chromeframe">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">activate Google Chrome Frame</a> to improve your experience.</p>
        <![endif]--> 

        <!-- This code is taken from http://twitter.github.com/bootstrap/examples/hero.html -->

        <div class="navbar navbar-inverse navbar-fixed-top">
            <div class="navbar-inner">
                <div class="container">
                    <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </a>
                    <a class="brand" href="<?php echo $set->url; ?>"><?php echo $set->site_name; ?></a>
                    <div class="nav-collapse collapse">
                        <ul class="nav" style="width:85%">
<?php
// we generate a simple menu this may need to be ajusted depending on your needs
// but it should be ok for most common items
foreach ($page->navbar as $key => $v) {
    if ($v[0] == 'item') {
    
        echo "\n\t\t<li".($v[1]['class'] ? " class='".$v[1]['class']."'" : "")."><a href='".$v[1]['href']."'>".$v[1]['name']."</a></li>";
    
    } else if($v[0] == 'dropdown') {

        echo "\n\t\t<li class='dropdown".($v['class'] ? " ".$v['class'] : "")."'".($v['style'] ? " style='".$v['style']."'" : "").">
            \t<a href='#' class='dropdown-toggle' data-toggle='dropdown'>".$v['name']." <b class='caret'></b></a>
            \n\t\t<ul class='dropdown-menu'>";
        foreach ($v[1] as $k => $v) 
            echo "\n\t\t\t<li".($v['class'] ? " class='".$v['class']."'" : "")."><a href='".$v['href']."'>".$v['name']."</a></li>";                                
        echo "\n\t\t</ul></li>";

    }
    
}


if(!$user->islg()) { 

echo "<span class='pull-right'>
        <a href='$set->url/register.php' class='btn btn-primary btn-small'>Sign Up</a>
        <a href='$set->url/login.php' class='btn btn-small'>Login</a>
    </span>
    ";
}

echo "</ul>

                    </div><!--/.nav-collapse -->
                </div>
            </div>
        </div>
";