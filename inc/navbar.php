<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
<nav class="navbar navbar-default">
    <div class="container-fluid">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="/formio/demo.php">DEMO</a>
        </div>

        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav">
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Menü <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li><a href="/formio/setformioconfig.php">Formfelder konfigurieren</a></li>
                        <li><a href="/formio/setlanguages.php">Sprachtexte konfigurieren</a></li>
                        <li role="separator" class="divider"></li>
                        <li><a href="/formio/saveform.php" target="_blank">Antrag anlegen</a></li>
                        <li><a class="dropdown-item" href="/osticket/scp/login.php" target="_blank">Ticketsystem</a></li>
                        <li role="separator" class="divider"></li>
                        <li><a class="dropdown-item" href="/formio/search.php">Datensuche</a></li>
                        <li><a class="dropdown-item" href="/formio/manageapi.php">Pull API Endpunkte</a></li>
                    </ul>
                </li>
                <li class="active"><a href="#"><?= $page_title ?></a></li>
            </ul>
        </div><!-- /.navbar-collapse -->
    </div><!-- /.container-fluid -->
</nav>
