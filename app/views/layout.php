<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title><?=$GLOBALS['sitename'] ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Javascript MVC Todo List Example">
    <meta name="author" content="Don Myers">
    <link href="/assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/application.css" rel="stylesheet">
    <script type="text/javascript" src="/assets/js/jquery-1.7.1.min.js"></script>
    <script type="text/javascript" src="/jmvc/index.js"></script>
  </head>
  <body>
    <div class="navbar navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <a class="brand">Todo</a>
          <ul class="nav">
          </ul>
<? if ($page == 'todo') { ?>
          <ul class="nav pull-right">
            <li id="btnlogout"><a href="/">Logout</a></li>
          </ul>
<? } ?>
        </div>
      </div>
    </div>
    <br><br><br>
    <div class="container">
			<?=$body ?>
    </div>
  </body>
</html>