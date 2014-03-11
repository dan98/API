<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="language" content="en" />
        <link rel="stylesheet" type="text/css" href="/css/main.css"/>
        <link rel="stylesheet" type="text/css" href="/css/bootstrap-responsive.css.css"/>
        <link rel="stylesheet" type="text/css" href="/css/bootstrap.css"/>
        <title><?php echo CHtml::encode($this->pageTitle); ?></title>
    </head>
    <body>
        <div class="navbar navbar-inverse navbar-fixed-top">
            <div class="navbar-inner">
                <div class="container">
                    <a class="brand" href="/">RatingAPI</a>
                    <div class="nav-collapse collapse">
                        <ul class="nav">
                            <li class="active">
                                <a href="/">RatingAPI</a>
                            </li>
                            <li class="">
                                <a href="/site/register">Register</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <?php echo $content; ?>

        <footer class="footer" style="margin-top: 0px;">
            <div class="container">
                <p>Designed and built by <a href="https://www.facebook.com/danu.kuriyoki" target="_blank">Daniel Grosu</a>.</p>
                <p>Code licensed under <a href="http://www.apache.org/licenses/LICENSE-2.0" target="_blank">Apache License v2.0</a>, documentation under <a href="http://creativecommons.org/licenses/by/3.0/">CC BY 3.0</a>.</p>
            </div>
        </footer>
    </body>
</html>