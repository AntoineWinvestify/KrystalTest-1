<?php

    // File: /app/views/Tests/csv/generate_spreadsheet.ctp

    // Loop through the data array
    foreach ($data as $row)
    {
       // Loop through every value in a row
        foreach ($row['Student'] as &$value)
        {
            // Apply opening and closing text delimiters to every value
            $value = "\"".$value."\"";
        }
        // Echo all values in a row comma separated
        echo implode(",",$row['Student'])."\n";
    }

?>