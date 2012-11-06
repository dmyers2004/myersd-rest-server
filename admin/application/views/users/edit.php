<?=form_open('/users/upsert/'.$user->id,array('id'=>'users_edit','class'=>'form-horizontal well')) ?>
  <input type="hidden" id="user_id" name="id" value="<?=$user->id ?>">
    <legend><?=$mode ?> User</legend>

    <div class="control-group username">
      <label class="control-label" for="input01">Name</label>
      <div class="controls">
        <?=form_input('name', $user->name, 'class="input-xlarge" id="username"') ?>
        <p class="help-block"><span class="label label-important">required</span></p>
      </div>
    </div>

    <div class="control-group email">
      <label class="control-label" for="input01">Email</label>
      <div class="controls">
        <?=form_input('email', $user->email, 'class="input-xlarge" id="email"') ?>
        <p class="help-block"><span class="label label-important">required</span></p>
      </div>
    </div>

    <div class="control-group password">
      <label class="control-label" for="input01">Password</label>
      <div class="controls">
        <?=form_input('password', $user->password, 'class="input-xlarge" id="password"') ?>
        <? if ($user->id == 0) { ?>
          <p class="help-block"><span class="label label-important">required</span></p>
        <? } else { ?>
          <p class="help-block">Enter a new password above to change the current password</p>
        <? } ?>
      </div>
    </div>

    <div class="control-group">
      <label class="control-label" for="select01">Access Group</label>
      <div class="controls">
        <?=form_dropdown('access_id', $access_options, $user->access_id) ?>
      </div>
    </div>

    <div class="control-group">
      <label class="control-label" for="input01">Account Active</label>
      <div class="controls">
        <label class="checkbox">
          <?=form_checkbox('active', '1', $user->active); ?>
          <label>is active</label>
        </label>
      </div>
    </div>

    <? if ($user->id != 0) { ?>
    <div class="control-group">
      <label class="control-label" for="input01">Last GUI Access</label>
      <div class="controls">
        <label class="control-label txt-al"><?=mysql_timestamp_format('F j, Y, g:i a',$user->last_gui_visit) ?></label>
      </div>
    </div>
    
    <div class="control-group">
      <label class="control-label" for="input01">Last Server Access</label>
      <div class="controls">
        <label class="control-label txt-al"><?=mysql_timestamp_format('F j, Y, g:i a',$user->last_server_visit) ?></label>
      </div>
    </div>
    <? } ?>
    
    <div class="txt-ar">
      <a class="btn" href="<?=base_url() ?>users">Cancel</a>
      <button name="btn" value="upsert" type="submit" class="btn btn-primary">Save</button>
    </div>
<?=form_close() ?>
