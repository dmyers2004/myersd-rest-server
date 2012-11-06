<? $asset->script('jquery.keyfilter.js'); ?> 
<?=form_open('/modules/process_create/',array('id'=>'access_edit','class'=>'form-horizontal well','enctype'=>'multipart/form-data')) ?>

  <input type="hidden" name="run" value="true">
  <legend>This will create a basic CRUD database model</legend>
  <input type="hidden" name="run" value="true">
  
  <div class="control-group name">
    <label class="control-label" for="input01">Model Name</label>
    <div class="controls">
      <?=form_input('name', '', 'class="input-xlarge" id="model_name" data-mask="lalphanum"') ?>
      <p class="help-block"><span class="label label-important">required</span> This is the name the model will have in the GUI this needs to be unique</p>
    </div>
  </div>

  <div class="control-group connection">
    <label class="control-label" for="input01">Server Connection Name</label>
    <div class="controls">
      <?=form_input('connection', '', 'class="input-xlarge" id="username"') ?>
      <p class="help-block"><span class="label label-info">Optional</span> This needs to be setup in the GUI under the settings tab </p>
    </div>
  </div>

  <div class="control-group tablename">
    <label class="control-label" for="input01">Table Name</label>
    <div class="controls">
      <?=form_input('tablename', '', 'class="input-xlarge" id="username"') ?>
      <p class="help-block"><span class="label label-info">Optional</span> The actual database table name</p>
    </div>
  </div>
   
  <div class="txt-ar">
    <a class="btn" href="<?=base_url() ?>modules">Cancel</a>
    <button name="btn" value="install" type="submit" class="btn btn-inverse">Install</button>
    <button name="btn" value="download" type="submit" class="btn btn-primary">Download</button>
  </div>
<?=form_close() ?>

