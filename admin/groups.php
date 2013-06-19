<?php
/// admin panel index

include "../inc/init.php";

if(!$user->isAdmin()) {
    header("Location: $set->url");
    exit;
}



$page->title = "Group Management";

$presets->setActive("adminpanel");



$act = isset($_GET['act']) ? $_GET['act'] : NULL;


if($_POST) {

    if($act == "group") {



    } else {



    }

}


include "../header.php";

?>
<div class="container-fluid">
<div class="row-fluid">
 <div class="span3">
   <div class="well sidebar-nav sidebar-nav-fixed">
		<ul class="nav nav-list">
			<li class="nav-header">ADMIN OPTIONS</li>
			<li><a href='index.php'>General Settings</a></li>
			<li class='active'><a href='groups.php'>Groups Management</a></li>
		</ul>
   </div><!--/.well -->
 </div><!--/span-->
 <div class="span9">
<?php
echo "
    <form class='form-horizontal' action='#' method='post'>
        <fieldset>

        <!-- Form Name -->
        <legend>Groups Management</legend>

        <!-- Text input-->
        <div class='control-group'>
          <label class='control-label' for='textinput'>Text Input</label>
          <div class='controls'>
            <input id='textinput' name='textinput' type='text' placeholder='placeholder' class='input-xlarge'>
            <p class='help-block'>help</p>
          </div>
        </div>

        <!-- Select Basic -->
        <div class='control-group'>
          <label class='control-label' for='selectbasic'>Select Basic</label>
          <div class='controls'>
            <select id='selectbasic' name='selectbasic' class='input-xlarge'>
              <option>Option one</option>
              <option>Option two</option>
            </select>
          </div>
        </div>

        <!-- Button -->
        <div class='control-group'>
          <label class='control-label' for='singlebutton'>Single Button</label>
          <div class='controls'>
            <button id='singlebutton' name='singlebutton' class='btn btn-primary'>Button</button>
          </div>
        </div>

        </fieldset>
    </form>";


?>

 </div><!--/span-->
</div><!--/row-->

</div><!--/.fluid-container-->



<?php
include '../footer.php';
?>