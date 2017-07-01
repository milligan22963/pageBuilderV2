<?php 

$document = '<html><head>';

$headerDoc =<<<HEADER_PIECE
<title>Element Test</title>
<link rel="stylesheet" href="http://localhost/site/css/site.css" type="text/css">
<script language="javascript" src="http://localhost/site/js/tools.js" type="text/javascript"> </script>
<script language="javascript" src="http://localhost/site/js/inheritance.js" type="text/javascript"> </script>
<script language="javascript" src="http://localhost/site/js/elements.js" type="text/javascript"> </script>
HEADER_PIECE;

$document .= $headerDoc;

$document .= '<div id="test_div" class="modal modal_dialog"></div>';
$document .= '<button onclick="javascript:Editor(\'test_div\')">test</button>';

$document .= '</head><body>';

$document .= '</body></html>';

echo $document;
?>