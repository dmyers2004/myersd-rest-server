<div class="navbar navbar-fixed-top">
  <div class="navbar-inner">
    <div class="container">
      <a class="brand"><?= $this->m_settings->cache['realm'] ?> <small><?=$this->m_settings->cache['gui_version'] ?></small></a>
        <ul class="nav">
<? if ($this->router->class != 'login') { ?>
          <li class="<?= ($class == 'dashboard') ? 'active' : ''; ?>"><a href="<?=base_url() ?>dashboard">Dashboard</a></li>
          <li class="<?= ($class == 'users') ? 'active' : ''; ?>"><a href="<?=base_url() ?>users">Users</a></li>
          <li class="<?= ($class == 'access') ? 'active' : ''; ?>"><a href="<?=base_url() ?>access">Access</a></li>
          <li class="<?= ($class == 'modules') ? 'active' : ''; ?>"><a href="<?=base_url() ?>modules">Modules</a></li>
          <li class="<?= ($class == 'settings') ? 'active' : ''; ?>"><a href="<?=base_url() ?>settings">Settings</a></li>
        </ul>
        <ul class="nav pull-right">
          <li><a href="<?=base_url() ?>login/logout">Logout</a></li>
<? } ?>
        </ul>
    </div>
  </div>
</div>
