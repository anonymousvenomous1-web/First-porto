<?php
require_once dirname(__DIR__, 3) . '/auth.php';
auth_start_session();
require_management_access('penulis');
