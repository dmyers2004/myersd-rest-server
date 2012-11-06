<form class="well">
<legend>Log Entry</legend>

  <table class="table table-bordered table-striped">
    <tbody>
      <tr>
        <td>Id</td>
        <td><span class="badge badge-inverse"><?=$entry->id ?></span></td>
      </tr>
      <tr>
        <td>Type</td>
        <td><?=gui_log_type($entry->type) ?></td>
      </tr>
      <tr>
        <td>Time</td>
        <td><i class="icon-calendar"></i> <?=mysql_timestamp_format('F j, Y, g:i a',$entry->time) ?></td>
      </tr>
      <tr>
        <td>User</td>
        <td><?=$entry->user_id ?> / <?=$entry->auth_user ?></td>
      </tr>
      <tr>
        <td>URL</td>
        <td><?=$entry->url ?></td>
      </tr>
      <tr>
        <td>Arguments</td>
        <td>
          <code><?=$entry->args ?></code>
        </td>
      </tr>
      <tr>
        <td>Model</td>
        <td><?=$entry->object ?></td>
      </tr>
      <tr>
        <td>Method</td>
        <td><?=$entry->method ?></td>
      </tr>
      <tr>
        <td>Request</td>
        <td>
          <pre><?=wordwrap(print_r(unserialize($entry->request),true),96,chr(10),true) ?></pre>
        </td>
      </tr>
      <tr>
        <td>Agent</td>
        <td><?=$entry->agent ?></td>
      </tr>
      <tr>
        <td>IP</td>
        <td><?=$entry->ip ?></td>
      </tr>
      <tr>
        <td>Memory / Peak Memory / Time</td>
        <td><?=formatBytes($entry->memory) ?> / <?=formatBytes($entry->pmemory) ?> / <?=$entry->etime ?></td>
      </tr>
    </tbody>
  </table>

	<div class="txt-ar">
		<a href="<?=base_url().$rtn ?>" class="btn btn-primary">Close</a>
	</div>
</form>
