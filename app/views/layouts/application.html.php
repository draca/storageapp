<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
      <title></title>
      <meta name="description" content="">
      <meta name="viewport" content="width=device-width">
      <link rel="stylesheet" href="/assets/css/normalize.css">
      <link rel="stylesheet" href="/assets/css/app.css">
  </head>
  <body>
    
    <header>
      <img src="/assets/img/logga.png" alt="">
    </header>

    <section>
      <?php
      if (file_exists(get_include_path().$yield)) {
        require_once($yield);
      }
      ?>
    </section>

    <footer>
      footer
    </footer>

  </body>
</html>
