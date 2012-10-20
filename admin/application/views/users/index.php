<h2 class="half">Users</h2>
<div class="half txt-ar"><a href="<?=base_url() ?>users/edit" class="btn btn-small btn-primary">New</a></div>
<table class="table table-bordered table-striped">
	<tr>
		<th>Name</th>
		<th>Email</th>
		<th>Access</th>
		<th class="txt-ac">Active</th>
		<th class="txt-ac">GUI Access</th>
		<th>Last Server Access</th>
		<th>Last GUI Access</th>
	</tr>
<?php foreach ($users as $rec) { ?>
	<tr>
    <td>
      <span class="floatl">
        <a data-id="<?=$rec->uid ?>" href="<?=base_url().'users/edit/'.$rec->uid ?>"><?=$rec->uname ?></a>
      </span>
      <span class="floatl icons1">
        <i data-id="<?=$rec->uid ?>" class="shifted icon-trash" style="display: none;"></i>
      </span>
    </td>
		<td><?=$rec->email ?></td>
		<td><a href="<?=base_url().'access/edit/'.$rec->access_id ?>"><?=$access_options[$rec->access_id] ?></a></td>
		<td class="txt-ac"><i class="icon-<?= ($rec->active == 1) ? 'ok' : 'remove' ?>"></i></td>
		<td class="txt-ac"><i class="icon-<?= ($rec->gui_access == 1) ? 'ok' : 'remove' ?>"></i></td>
		<td><?=mysql_timestamp_format('F j, Y, g:i a',$rec->last_server_visit) ?></td>
		<td><?=mysql_timestamp_format('F j, Y, g:i a',$rec->last_gui_visit) ?></td>
	</tr>
<? } ?>
</table>
