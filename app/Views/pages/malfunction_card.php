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