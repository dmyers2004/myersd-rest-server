<?php $this->load->view('partials/dashboard_msgs') ?>
  <h2 class="half">Dashboard</h2>
  <div class="half txt-ar">
    <?php if ($filter) { ?>
      <a class="btn btn-small" href="<?=base_url() ?>dashboard/<?=$page ?>"><i class="icon-refresh"></i> Clear User Filter</a>
    <?php } ?>
    <a data-id="1" data-which="GUI Log" class="shiftdashboard btn btn-small <? echo ($type == 1) ? 'btn-info' : ''; ?>" href="<?=base_url() ?>dashboard"><i class="icon-user"></i> GUI Log</a>
    <a data-id="2" data-which="Server Log" class="shiftdashboard btn btn-small <? echo ($type == 2) ? 'btn-info' : ''; ?>" href="<?=base_url() ?>dashboard/server"><i class="icon-fire"></i> Server Log</a>
  </div>
  <pre class="prettyprint linenums cfloat">
<?php
  foreach ($log as $line) {
    echo '<a href="'.base_url().'dashboard/details/'.$line->id.'">'.pad($line->time,16).'</a>';
    if (!$filter) {
      echo '<a href="'.base_url().'dashboard/'.$page.'/'.$line->user_id.'">'.pad($line->user_id,18).'</a>';
    } else {
      echo pad($line->user_id,18);
    }
    echo pad(substr($line->etime,0,8),8).pad(formatBytes($line->pmemory),8);
    echo pad($line->method.':'.$line->object.'/'.$line->args,50);
  }
?>
<div style="clear: both;"></div></pre>