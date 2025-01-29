<form>
  <div class="input-group px-2 mb-1">
    <a class="input-group-text" href="/">на главную</a>
    <span class="input-group-text">Зафиксированная неисправность</span>
    <select name="id" class="form-select form-select-sm" required>
      <option value="" <?=$val ? '' : 'selected'?>>не выбрано</option>
      <?php foreach($options as $option):?>
        <option value="<?=$option?>" <?=$val===$option ? 'selected' : ''?>>
          <?=$option?>
        </option>
      <?php endforeach;?>
    </select>
    <div class="d-flex justify-content-center">
      <button type="submit" class="btn btn-primary my-auto">Запросить</button>
    </div>
  </div>
</form>
