<?php
  helper('form');
  $isTimestamp = FALSE;
?>
<a class="position-fixed top-0 start-0" href="/">на гравную</a>
<h3 class="text-center"><?=$heading?></h3>
<form method="POST">
  <?php foreach($data as $column => $val): ?>
  <div class="input-group px-2 mb-1">
    
    <?php if('checkbox' === ($description[$column]['type'] ?? 'text')):?>
      <label for="id-<?=$column?>"><span class="input-group-text"><?=$description[$column]['label']?></span></label>
      <div class="input-group-text">
        <input type="checkbox" <?=$val ? 'checked' : ''?> id="id-<?=$column?>" name="<?=$column?>" class="form-check-input mt-0">
      </div>
    
    <?php elseif('select' === ($description[$column]['type'] ?? 'text')): ?>
      <span class="input-group-text"><?=$description[$column]['label']?></span>
      <select name="<?=$column?>" class="form-select form-select-sm" <?=$description[$column]['required'] ?? ''?>>
        <option value="" <?=$val ? '' : 'selected'?>>не выбрано</option>
        <?php foreach($description[$column]['options'] as $option):?>
          <option value="<?=$option['id']?>" <?=$val===$option['id'] ? 'selected' : ''?>>
            <?=$option['value']?>
          </option>
        <?php endforeach;?>
      </select>

    <?php elseif('datetime' === ($description[$column]['type'] ?? 'text')): ?>
      <?php $datapickers[] = "datepicker-$column";?>
      <span class="input-group-text"><?=$description[$column]['label']?></span>
      <input 
        type="datetime-local"
        step=1
        class="form-control" 
        name="<?=$column?>" 
        value="<?=esc($val)?>" 
        <?=$description[$column]['required']?>
      />

      <?php if(!$isTimestamp): 
        $isTimestamp = TRUE;
      ?>
        <input type="text" id="client-timezone-name" name="timezone_name" class="d-none"/>
      <?php endif; ?>

    <?php elseif('text' === ($description[$column]['type'] ?? 'text')): ?>
      <span class="input-group-text"><?=$description[$column]['label']?></span>
      <input type="<?=$description[$column]['type'] ?? 'text'?>" class="form-control" name="<?=$column?>" value="<?=esc($val)?>" <?=$description[$column]['required']?>>
    
    <?php endif; ?>
  </div>
  <?php endforeach; ?>

  <div class="d-flex justify-content-center">
    <button type="submit" class="btn btn-primary my-auto">Записать</button>
  </div>
</form>

<?php if($isTimestamp): ?>
  <script>
    setInterval(function() {
      // let offset = new Date().getTimezoneOffset();
      // let offsetHours = -(offset / 60); // Преобразуем в часы и меняем знак на противоположный
      // document.getElementById('client-timezone').value = (offsetHours >= 0 ? "+" : "-") + offsetHours;
      document.getElementById('client-timezone-name').value = Intl.DateTimeFormat().resolvedOptions().timeZone;
    }, 1000);
  </script>
<?php endif; ?>
