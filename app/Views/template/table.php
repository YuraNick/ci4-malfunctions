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
