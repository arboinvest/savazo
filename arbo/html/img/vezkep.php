<?php
ob_clean();
header('Content-Type: image/png');
readfile('./vezerlo.png');
