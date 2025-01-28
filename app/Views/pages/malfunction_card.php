<form>
  <div class="input-group px-2 mb-1">
    <a class="input-group-text" href="/">на гравную</a>
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

<h4>Неисправность:</h4>
<?php foreach($data['malfunction'] as $malfunction): ?>
  <table>
    <?php foreach($malfunction as $key => $val): ?>
      <tr>
        <td><?=$key?></td>
        <td><?=$val?></td>
      </tr>
    <?php endforeach; ?>
  </table>
<?php endforeach; ?>
<br>
<h4>Статусы и комментарии диспетчеров:</h4>
<?php foreach($data['dispatcher_confirms'] as $dispatcher_confirm): ?>
  <table>
    <?php foreach($dispatcher_confirm as $key => $val): ?>
      <tr>
        <td><?=$key?></td>
        <td><?=$val?></td>
      </tr>
    <?php endforeach; ?>
  </table>
<?php endforeach; ?>
<br>
<h4>Вопросы диспетчеров в техподдержку:</h4>
<?php foreach($data['dispatcher_support_questions'] as $row): ?>
  <table>
    <?php foreach($row as $key => $val): ?>
      <tr>
        <td><?=$key?></td>
        <td><?=$val?></td>
      </tr>
    <?php endforeach; ?>
  </table>
<?php endforeach; ?>
<br>
<h4>Ответы техподдержки на вопросы диспетчеров:</h4>
<?php foreach($data['dispatcher_support_answers'] as $row): ?>
  <table>
    <?php foreach($row as $key => $val): ?>
      <tr>
        <td><?=$key?></td>
        <td><?=$val?></td>
      </tr>
    <?php endforeach; ?>
  </table>
<?php endforeach; ?>
<br>
<h4>Вопросы техподдержки разработчикам:</h4>
<?php foreach($data['support_developer_questions'] as $row): ?>
  <table>
    <?php foreach($row as $key => $val): ?>
      <tr>
        <td><?=$key?></td>
        <td><?=$val?></td>
      </tr>
    <?php endforeach; ?>
  </table>
<?php endforeach; ?>
<br>
<h4>Ответы разработчиков техподдержке:</h4>
<?php foreach($data['support_developer_answers'] as $row): ?>
  <table>
    <?php foreach($row as $key => $val): ?>
      <tr>
        <td><?=$key?></td>
        <td><?=$val?></td>
      </tr>
    <?php endforeach; ?>
  </table>
<?php endforeach; ?>



<!-- <pre><?= print_r($data, true) ?></pre>   -->