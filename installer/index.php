<?php
if (isset($_POST['run'])) {
  require('process.php');
  die();
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>MyRESTful Server</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
  </head>
  <body>
<div class="navbar navbar-fixed-top">
  <div class="navbar-inner">
    <div class="container">
      <a class="brand">MyRESTful Server Installer</a>
        <ul class="nav">
        </ul>
    </div>
  </div>
</div>
<div class="container">
<br><br><br>
<form id="FormName" action="" method="post" name="FormName" enctype="multipart/form-data" target="_top" class="form-horizontal well">
  <legend>This is a VERY VERY simple installer no error checking is done here</legend>
  <input type="hidden" name="run" value="true">
  
  <label>Admin Name</label>
  <input type="text" name="name" value="" size="69" maxlength="64" class="span6">

  <br><br>
  <label>Admin Email</label>
  <input type="text" name="email" value="" size="69" maxlength="64" class="span6">
  
  <br><br>
  <label>Admin Password</label>
  <input type="text" name="password" value="" size="69" maxlength="64" class="span6">
  
  <br><br>
  <label>MySQL Host</label>
  <input type="text" name="host" value="" size="69" maxlength="64" class="span6">
  
  <br><br>
  <label>MySQL User</label>
  <input type="text" name="dbname" value="" size="69" maxlength="64" class="span6">
  
  <br><br>
  <label>MySQL Password</label>
  <input type="text" name="dbpassword" value="" size="69" maxlength="64" class="span6">

  <br><br>
  <label>MySQL database</label>
  <input type="text" name="database" value="" size="69" maxlength="64" class="span6">
  <p class="help-block">You will need to create this database first</p>

  <br><br>
  <label>GUI Folder Name</label>
  <input type="text" name="guifolder" value="admin" size="69" maxlength="64" class="span6">
  <p class="help-block">If you renamed your admin folder to something else please enter it here<br>If you leave it "admin" you will not be able to have a model named admin</p>

  <br>
  <div class="txt-ar">
    <input type="submit" class="btn btn-primary">
  </div>  
</form>
</body>
</html>