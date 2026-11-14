<?php
require_once dirname(__DIR__, 2) . '/auth.php';
auth_start_session();
require_management_access('siswa');
