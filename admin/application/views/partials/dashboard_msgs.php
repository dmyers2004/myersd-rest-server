<? if ($installer) { ?>
<div class="alert">
  <a class="close" data-dismiss="alert">&times;</a>
  <strong>Warning!</strong> Remove your installer folder!
</div>
<? } ?>

<? if ($firsttime) { ?>
<div class="alert alert-info">
  <a class="close" data-dismiss="alert">&times;</a>
  <strong>Note</strong> First time setup complete. You will still need to assign access to the Admin Group &bullet; <a href="<?=base_url() ?>access/edit/1">Click Here</a>
</div>
<? } ?>