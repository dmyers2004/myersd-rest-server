<!DOCTYPE html>
<html lang="en">
  <head>
    <title><?=$this->m_settings->cache['realm'] ?></title>
    <?=$this->asset->header() ?>
    <script>
      var base_url = '<?=base_url() ?>';
    </script>
  </head>
  <body id="<?=$class ?>">
  <?php $this->load->view('partials/navbar') ?>
  <div class="container">
