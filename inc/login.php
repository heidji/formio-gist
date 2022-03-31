<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
      integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
<?php

$logged_in = false;
$bad_login = false;
if(isset($_POST['username']) && isset($_POST['password'])){
    if($_POST['username'] == 'ostadmin' && $_POST['password'] == 'Admin1'){
        $_COOKIE['username'] = 'ostadmin';
        $_COOKIE['password'] = md5('Admin1');
        setcookie('username', 'ostadmin',time()+7*24*60*60, "/");
        setcookie('password', md5('Admin1'),time()+7*24*60*60, "/");
    }else{
        $bad_login = true;
    }
}
if(isset($_COOKIE['username']) && isset($_COOKIE['password'])){
    if($_COOKIE['username'] == 'ostadmin' && $_COOKIE['password'] == md5('Admin1')){
        $logged_in = true;
    }
}
if(!$logged_in): ?>
<div class="container" style="display: flex; flex-direction: row; align-items: center; justify-content: center">
    <div class="container" style="margin-top: 200px; display: flex; flex-direction: column; align-items: center; justify-content: center">
        <?php if($bad_login): ?>
            <div class="alert alert-danger" role="alert">
                Ungültige Username / Passwort Kombination
            </div>
        <?php endif; ?>
        <form action="<?= $_SERVER['PHP_SELF'] ?>" method="post">
            <div class="form-group">
                <label for="exampleInputEmail1">Username</label>
                <input type="text" class="form-control" name="username" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter email">
            </div>
            <div class="form-group">
                <label for="exampleInputPassword1">Passwort</label>
                <input type="password" class="form-control" name="password" id="exampleInputPassword1" placeholder="Password">
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
</div>
<?php endif; ?>
<?php if(!$logged_in) exit; ?>
