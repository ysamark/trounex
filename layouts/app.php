<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>APP - Automação de Processos Penitenciário</title>
  <link rel="shortcut icon" href="<?= image ('icons/favicon-16x16.png') ?>" type="image/png">
  <script type="text/javascript" src="<?= asset_path ('js', 'uolkeo.min.js') ?>" crossorigin="annonimous"></script>
  <!--script src="https://cdn.jsdelivr.net/npm/cep-promise/dist/cep-promise.min.js"></script-->
  
  <?= style ('bootstrap.css') ?>
  <?= style ('font-awesome.min.css') ?>
  <?= style ('custom-select-box.css') ?>
  <?= style ('main.css') ?>
  <?= style ('app.css') ?>
  <?= style ('dashboard.css') ?>
  <?= style ('data-dropdown-menu.css') ?>
  <?= style ('data-form.css') ?>

  <?= script ('config.js') ?>
  <?= script ('build/application.bundle.js') ?>
</head>
<body>
  <?php App\render(); ?>
</body>
</html>
