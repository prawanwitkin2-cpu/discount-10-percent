<?php

require dirname(__DIR__) . '/app/bootstrap.php';

logout_admin();
redirect('/admin/login.php');

