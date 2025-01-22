<?php
  helper('form');
?>
<a class="position-fixed top-0 start-0" href="/">на гравную</a>
<h3 class="text-center"><?=$heading?></h3>
<form method="POST">
  <?php foreach($data as $column => $val): ?>
  <div class="input-group px-2 mb-0">
    <?php if(($description[$column]['type'] ?? 'text') === 'checkbox'):?>
      <label for="id-<?=$column?>"><span class="input-group-text"><?=$description[$column]['label']?></span></label>
      <div class="input-group-text">
        <input type="checkbox" <?=$val ? 'checked' : ''?> id="id-<?=$column?>" name="<?=$column?>" class="form-check-input mt-0">
      </div>
    <?php elseif(($description[$column]['type'] ?? 'text') === 'select'): ?>
      <span class="input-group-text"><?=$description[$column]['label']?></span>
      <select class="form-select form-select-sm">
        <option selected>не выбрано</option>
        <?php foreach($description[$column]['options'] as $option):?>
          <option name="<?=$option['id']?>"><?=$option['value']?></option>
        <?php endforeach;?>
      </select>
    <?php else: ?>
      <span class="input-group-text"><?=$description[$column]['label']?></span>
      <input type="<?=$description[$column]['type'] ?? 'text'?>" class="form-control" name="<?=$column?>" value="<?=esc($val)?>" <?=$description[$column]['required']?>>
    <?php endif; ?>
  </div>

  <div class="input-group px-2 mb-2">
    
  </div>
  <?php endforeach; ?>
  <div class="d-flex justify-content-center">
    <button type="submit" class="btn btn-primary my-auto">Записать</button>
  </div>
</form>
