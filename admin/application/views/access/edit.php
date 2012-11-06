<?=form_open('/access/upsert/'.$access->id,array('id'=>'access_edit','class'=>'form-horizontal well')) ?>
  <input type="hidden" id="access_id" name="id" value="<?=$access->id ?>">
    <legend><?=$mode ?> Access</legend>

    <div class="control-group access_name">
      <label class="control-label" for="input01">Name</label>
      <div class="controls">
        <?=form_input('name', $access->name, 'class="input-xlarge" id="access_name"') ?>
        <p class="help-block"><span class="label label-important">required</span></p>
     </div>
    </div>

    <div class="control-group">
      <label class="control-label" for="input01">GUI Access</label>
      <div class="controls">
        <label class="checkbox">
          <?=form_checkbox('gui_access', '1', $access->gui_access); ?>
          Allow
        </label>
      </div>
    </div>

    <div class="control-group">
      <label class="control-label" for="input01">Model Access</label>
      <div class="controls"></div>
    </div>
<? foreach ($resource as $obj => $methods) { ?>
    <div class="control-group resource_line">
      <div class="resourcel"><?=substr($obj,6) ?></div>
      <div class="resourcer">
      <? foreach ($methods as $m) { ?>
        <div class="resourcei"><?=form_checkbox('access['.$m->id.']', $m->id, resource($m->id,$access_resource),'data-group="'.$obj.'" class="floatl"'); ?> <?=highlight_defaults($m->method) ?></div>
      <? } ?>
      </div>
    </div>
<? } ?>
    <div class="control-group">
      <div class="resourcel">&nbsp;</div>
      <div class="resourcer">
        <p class="help-block">
          Hold down shift to select/unselect all in the same model<br>
          <span class="badge badge-info"></span>&nbsp;REST Methods<br>
          <span class="badge badge-error"></span>&nbsp;Other Access Checkable Resources<br>
        </p>
      </div>
    </div>

    <div class="txt-ar">
      <a class="btn" href="<?=$_SERVER["HTTP_REFERER"] ?>">Cancel</a>
      <button name="btn" value="upsert" type="submit" class="btn btn-primary">Save</button>
    </div>
<?=form_close() ?>
