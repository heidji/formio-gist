<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<?php

$logins = [
    'samir' => 'Admin1',
    'ostadmin' => 'Admin1',
];

$logged_in = false;
$bad_login = false;
if(isset($_POST['username']) && isset($_POST['password'])){
    if(in_array($_POST['username'], array_keys($logins)) && md5($_POST['password']) == md5($logins[$_POST['username']])){
        $_COOKIE['username'] = $_POST['username'];
        $_COOKIE['password'] = md5($logins[$_POST['username']]);
        setcookie('username', $_POST['username'],time()+7*24*60*60, "/");
        setcookie('password', md5($logins[$_POST['username']]),time()+7*24*60*60, "/");
    }else{
        $bad_login = true;
    }
}
if(isset($_COOKIE['username']) && isset($_COOKIE['password'])){
    if(in_array($_COOKIE['username'], array_keys($logins))  && $_COOKIE['password'] == md5($logins[$_COOKIE['username']])){
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
