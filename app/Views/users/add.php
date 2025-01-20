<?php
  helper('form');
  $options = [
    'user' => 'пользователь',
    'dispatcher' => 'диспетчер',
    'support' => 'техподдержка',
    'developer' => 'разработчик',
  ];
?>
<h3 class="text-center">Добавление пользователя</h3>
<form method="POST">
  <div class="input-group px-2 mb-2">
    <span class="input-group-text">Логин</span>
    <input type="text" class="form-control" name="login" value="<?=esc($login)?>" required>
  </div>
  <div class="input-group px-2 mb-2">
    <span class="input-group-text">Наименование пользователя</span>
    <input type="text" class="form-control" name="name" value="<?=esc($name)?>" required>
  </div>
  <div class="input-group px-2 mb-2">
    <label class="input-group-text" for="email">E-mail</label>
    <input class="form-control" type="email" name="email" aria-describedby="emailHelp" value="<?=esc($email)?>" required>
  </div>
  <div class="input-group px-2 mb-2">
    <label class="input-group-text">Роль пользователя</label>
    <select class="form-select" name="role">
      <?php foreach ($options as $val => $text): ?>
        <option <?=$val===$role ? 'selected' : ''?> value="<?=$val?>"><?=$text?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <!-- <div class="mb-3 form-check">
    <input type="checkbox" class="form-check-input" id="exampleCheck1">
    <label class="form-check-label" for="exampleCheck1">Check me out</label>
  </div> -->
  <div class="d-flex justify-content-center">
    <!-- <button class="btn btn-default">Centered button</button> -->
    <button type="submit" class="btn btn-primary my-auto">Записать</button>
  </div>
</form>
