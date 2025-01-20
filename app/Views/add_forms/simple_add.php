<?php
  helper('form');
?>
<h3 class="text-center"><?=$heading?></h3>
<form method="POST">
  <?php foreach($data as $column => $val): ?>
  <div class="input-group px-2 mb-2">
    <span class="input-group-text"><?=$description[$column]['label']?></span>
    <input type="text" class="form-control" name="<?=$column?>" value="<?=esc($val)?>" <?=$description[$column]['required']?>>
  </div>
  <?php endforeach; ?>
  <div class="d-flex justify-content-center">
    <!-- <button class="btn btn-default">Centered button</button> -->
    <button type="submit" class="btn btn-primary my-auto">Записать</button>
  </div>
</form>
