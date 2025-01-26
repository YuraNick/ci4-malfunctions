<?php
  $options = [
    'user' => 'пользователь',
    'dispatcher' => 'диспетчер',
    'support' => 'техподдержка',
    'developer' => 'разработчик',
  ];
?>
<a class="position-fixed top-0 start-0" href="/">на гравную</a>
<h3 class="text-center">Пользователи</h3>

<table class='table table-bordered' id='data-table'>
  <thead>
    <tr>
      <?php foreach($columns as $column): ?>
      <th><?=$column?></th>
      <?php endforeach ?>
    </tr>
  </thead>
  <tbody>
    <?php foreach($rows as $row): ?>
      <tr>
        <?php foreach($columns as $column): ?>
        <td><?=$row[$column]?></td>
        <?php endforeach ?>
      </tr>
    <?php endforeach ?>
  </tbody>
</table>
