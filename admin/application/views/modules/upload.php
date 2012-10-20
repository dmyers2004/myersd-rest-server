<?=form_open_multipart('/modules/process_upload/',array('id'=>'access_edit','class'=>'form-horizontal well')) ?>
  <input type="hidden" id="access_id" name="id" value="<?=$access->id ?>">
    <legend>Upload a New Module</legend>
		
    <div class="control-group access_name">
      <label class="control-label" for="input01">Select Module</label>
      <div class="controls">
        <input name="upload" class="input-file" id="fileInput" type="file">
      </div>
    </div>

    <div class="txt-ar">
      <a class="btn" href="<?=base_url() ?>modules">Cancel</a>
      <button name="btn" value="upsert" type="submit" class="btn btn-primary">Process</button>
    </div>
<?=form_close() ?>

