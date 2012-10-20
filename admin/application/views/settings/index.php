<h2 class="half">Settings</h2>
<div class="half txt-ar"><a href="<?=base_url() ?>settings/edit" class="btn btn-small btn-primary">New</a></div>
<table class="table table-bordered table-striped">
  <tr>
    <th>Slug</th>
    <th>Value</th>
    <th class="txt-ac">Used On</th>
    <th class="txt-ac">Protected</th>
  </tr>
<?php foreach ($settings as $rec) { ?>
  <tr>
    <td<?=tooltip($rec->usage,'Usage') ?>>
      <span class="floatl">
        <a href="<?=base_url().'settings/edit/'.$rec->id ?>" data-id="<?=$rec->id ?>"><?=$rec->slug ?></a>
      </span>
      <span class="floatl icons1">
        <i data-id="<?=$rec->id ?>" class="shifted icon-trash" style="display: none;"></i>
      </span>
		</td>
    <td><?=hidepass($rec->value,$rec->slug) ?></td>
    <td class="txt-ac"><?=usedon_icon($rec->usedon) ?></td>
		<td class="txt-ac"><i class="icon-<?= ($rec->root == 1) ? 'lock' : '' ?>"></i></td>
  </tr>
<? } ?>
</table>
<span class="label label-important">NOTE: Edit these settings with caution</span>
<br>
<span class="label label-info">Mouse Over each slug for a short description of it's usage</span>
