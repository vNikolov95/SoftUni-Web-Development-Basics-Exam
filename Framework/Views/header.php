<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Framework</title>
    <link rel="stylesheet" href="<?=\Framework\Helpers\Helpers::url()?>Css/style.css" type="text/css">
    <link rel="stylesheet" href="<?=\Framework\Helpers\Helpers::url()?>Css/bootstrap.min.css" type="text/css">
    <script src="<?=\Framework\Helpers\Helpers::url()?>Js/Libs/jquery-2.1.4.min.js" type="application/javascript"></script>
    <script src="<?=\Framework\Helpers\Helpers::url()?>Js/Libs/bootstrap.min.js"></script>
    <script src="<?=\Framework\Helpers\Helpers::url()?>Js/Libs/alert.js"></script>
</head>
<body>
<header>
    <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
        <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-9" aria-expanded="false">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="navbar-collapse collapse" id="bs-example-navbar-collapse-9" aria-expanded="false">


                    <?php if(! \Framework\Core\Identity::isUserLogged() ): ?>
                    <ul class="nav navbar-nav navbar-right">
                        <li><a href="<?= \Framework\Helpers\Helpers::url() . 'login'?>" class="hvr-underline-reveal"><span class="glyphicon glyphicon-log-in"></span> Login</a></li>
                        <li><a href="<?= \Framework\Helpers\Helpers::url() . 'register'?>" class="hvr-underline-reveal"><span class="glyphicon glyphicon-registration-mark"></span> Register</a></li>
                    </ul>
                    <?php else: ?>
                        <ul class="nav navbar-nav">
                            <li><a href="<?= \Framework\Helpers\Helpers::url() . 'profile'?>" class="hvr-underline-reveal"><span class="glyphicon glyphicon-user"></span> Profile</a></li>
                        </ul>
                        <ul class="nav navbar-nav navbar-right">
                            <li><a href="<?= \Framework\Helpers\Helpers::url() . 'logout'?>" class="hvr-underline-reveal"><span class="glyphicon glyphicon-log-out"></span> Logout</a></li>
                        </ul>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
</header>

<div class="container">