<?php

ob_start();
require_once OPTIPIC_LOAD_TEMPLATE_PATH;
$content = ob_get_contents();
ob_end_clean();

//change content
$content = optipic_change_content($content);
//$content = str_replace('<head>', '<head test="test">', $content);

echo $content;


?>