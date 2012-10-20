<h2 class="half">Access</h2>
<div class="half txt-ar"><a href="<?=base_url() ?>access/edit" class="btn btn-small btn-primary">New</a></div>
<table class="table table-bordered table-striped">
  <tr>
    <th>Name</th>
    <th class="txt-ac">GUI Access</th>
  </tr>
<?php foreach ($access as $rec) { ?>
  <tr>
    <td>
      <span class="floatl">
        <a data-id="<?=$rec->id ?>" href="<?=base_url().'access/edit/'.$rec->id ?>"><?=$rec->name ?></a>
      </span>
      <span class="floatl icons1">
        <i data-id="<?=$rec->id ?>" class="shifted icon-trash" style="display: none;"></i>
      </span>
    </td>
    <td class="txt-ac"><i class="icon-<?= ($rec->gui_access == 1) ? 'ok' : 'remove' ?>"></i></td>
  </tr>
<? } ?>
</table>
