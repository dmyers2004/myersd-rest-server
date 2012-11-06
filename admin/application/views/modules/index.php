<h2 class="half">Modules</h2>
<div class="half txt-ar">
  <a href="<?=base_url() ?>modules/create" class="btn btn-mini">Create DB Model</a>
  <img width="64" height="0">
  <a href="<?=base_url() ?>modules/upload" class="btn btn-small btn-primary">Upload</a>
</div>
<table class="table table-bordered table-striped">
  <tr>
    <th>Name</th>
    <th class="txt-ac">Added</th>
    <th class="txt-ac">Version</th>
    <th class="txt-ac">Type</th>
    <th>Accessable Methods</th>
  </tr>
<?php foreach ($resource as $obj => $methods) { ?>
  <tr>
    <td>
      <span class="floatl">
        <?=substr($obj,6) ?>
      </span>
      <span class="floatl icons2">
        <i data-id="<?=$obj ?>" class="shifted icon-trash" style="display: none;"></i>
        <i data-id="<?=$obj ?>" class="shifted download icon-arrow-down" style="display: none;"></i>
      </span>
    </td>
    <td class="txt-ac"><?=mysql_timestamp_format('n/j/Y',$methods[0]->created) ?></td>
    <td class="txt-ac"><?=$methods[0]->version ?></td>
    <td class="txt-ac"><?=getmodeltype($methods[0]->type) ?><br><small><?=$methods[0]->dbconnection ?></small></td>
		<td>
			<? foreach ($methods as $m) { ?>
				<div class="resourcei"><?=highlight_defaults($m->method) ?></div>
			<? } ?>
		</td>
  </tr>
<? } ?>
</table>
<span class="badge badge-info"></span>&nbsp;REST Methods<br>
<span class="badge badge-error"></span>&nbsp;Other Accessable Methods used in combination with the REST Methods<br>
<span class="label label-important">NOTE: Upload modules with caution since they contain PHP code</span>
