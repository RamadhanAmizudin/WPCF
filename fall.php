<?php
require './init.php';

$id = request('id');
$hash = request('hash');
if(!is_numeric($id)) {
    die('Invalid ID.');
}

if(!preg_match('/[a-f0-9]{32}/i', $hash)) {
    die('Invalid Hash.');
}

$info = $wpcf->getStatus($id, $hash);

if( !$info ) {
    die('Invalid Hash and ID.');
}

$wpcf->saveData($id, $hash, server('REMOTE_ADDR'));
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../favicon.ico">

    <title>Cover Template for Bootstrap</title>
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>

  <body>
  </body>
</html>
