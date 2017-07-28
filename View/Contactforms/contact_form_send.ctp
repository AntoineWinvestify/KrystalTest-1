<?php
if ($result[0] == 1) {
    $result[1] = __("Message sent");
    echo "1";
}
else {
    echo "0";
}

echo json_encode($result );
