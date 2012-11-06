<? $asset->script('jquery.keyfilter.js'); ?> 
<?=form_open('/settings/upsert/'.$rec->id,array('id'=>'settings_edit','class'=>'form-horizontal well')) ?>
  <input type="hidden" id="settings_id" name="id" value="<?=$rec->id ?>">
    <legend><?=$mode ?> Setting</legend>

    <div class="control-group slug">
      <label class="control-label" for="input01">Slug</label>
      <div class="controls">     
        <? if (!$rec->root) { ?>
          <?=form_input('slug', $rec->slug, 'data-mask="alphanum" class="input-xlarge" id="slug"') ?>
          <p class="help-block"><span class="label label-important">required</span></p>
        <? } else { ?>
          <label class="control-label txt-al"><?=$rec->slug ?></label>
          <input type="hidden" name="slug" value="<?=$rec->slug ?>">
        <? } ?>
      </div>
    </div>

    <div class="control-group value">
      <label class="control-label" for="input01">Value</label>
      <div class="controls">
        <?=form_input('value', $rec->value, 'class="input-xlarge span7" id="value"') ?>
        <p class="help-block"><span class="label label-important">required</span></p>
      </div>
    </div>

    <? if (!$rec->root) { ?>
    <div class="control-group usage">
      <label class="control-label" for="input01">Usage</label>
      <div class="controls">
        <?=form_input('usage', $rec->usage, 'class="input-xlarge span7" id="usage"') ?>
      </div>
    </div>		
    <? } else { ?>
      <input type="hidden" name="usage" value="<?=$rec->usage ?>">
    <? } ?>

    <? if (!$rec->root) { ?>
    <div class="control-group">
      <label class="control-label" for="select01">Used On</label>
      <div class="controls">
        <?=form_dropdown('usedon', $usedon_options, $rec->usedon) ?>
      </div>
    </div>
    <? } else { ?>
      <input type="hidden" name="usedon" value="<?=$rec->usedon ?>">
    <? } ?>
    		
    <div class="txt-ar">
      <a class="btn" href="<?=base_url() ?>settings">Cancel</a>
      <button name="btn" value="upsert" type="submit" class="btn btn-primary">Save</button>
    </div>
<?=form_close() ?>

