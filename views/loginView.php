<?php require_once('layouts/head.php'); ?>

<div class="container">
    <div class="row login">
        <div class="main-login">
            <span style="color:<?php echo $message_color; ?>"><?php echo $message; ?></span>
            <form action="login" method="post" class="form-login">
                <div class="form-group">
                    <div class="cols-sm-10">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-user" aria-hidden="true"></i></span>
                            <input type="text" class="form-control" name="user" placeholder="Usuario" required >
                        </div>
                    </div>
                </div>   
                <div class="form-group">
                    <div class="cols-sm-10">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-lock" aria-hidden="true"></i></span>
                            <input type="password" class="form-control" name="password" placeholder="Contraseña" required >
                        </div>
                     </div>
                </div>     
                <div class="form-check">
                    <input type="checkbox" value="true" name="save"> No cerrar sesión
                </div>
                <button class="form-button" type="submit" class="btn btn-primary">Entrar</button>
            </form>
        </div>    
    </div>
</div>

<?php require_once('layouts/footer.php'); ?>

<script src="js/javascript.js"></script>

<?php require_once('layouts/end.php'); ?>