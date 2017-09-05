
<?php require_once('layouts/head.php'); ?>

<?php if(!$login){ ?>
<form action="" method="post">
    <label for="user">Nombre:</label><input type="text" name="user" required ><br><br>
    <label for="password">Contraseña:</label><input type="password" name="password" required ><br><br>
    <label for="save">Guardar</label><input type="checkbox" value="true" name="save" ><br><br>
    <input type="hidden" name="login" value=true>
    <button type="submit">Entrar</button>
</form>
<?php }else{ ?>
<form action="" method="post">
    <input type="hidden" name="logout" value=true>
    <button type="submit">Salir</button>
</form>
<?php } ?>

<?php require_once('layouts/footer.php'); ?>

<script src="js/javascript.js"></script>

<?php require_once('layouts/end.php'); ?>