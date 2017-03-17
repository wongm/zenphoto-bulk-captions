<?php

define('OFFSET_PATH', 3);
require_once(dirname(dirname(dirname(__FILE__))) . '/zp-core/admin-globals.php');
require_once(dirname(dirname(dirname(__FILE__))) . '/zp-core/template-functions.php');
require_once(dirname(dirname(__FILE__)) . '/photostream.php');
admin_securityChecks(ALBUM_RIGHTS, currentRelativeURL());
require_once('functions.php');

saveBulkCaptions();
initBulkCaptionData();
include_once('form.php');
?>