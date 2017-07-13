<?php
class HelloShell extends AppShell {
    public function main() {
        $this->out('Hello world.');
        
        
	echo "this is the test script running \n";
	$serial_result = "Antoine de Poorter";
	$fhandle = fopen("/home/antoine/testname_own_dir.txt", "a+");
	fwrite($fhandle, $serial_result);
	fclose($fhandle);
 	$fhandle1 = fopen("testname.txt", "a+");
	fwrite($fhandle1, $serial_result);
	fclose($fhandle1);
	

        
        
        
        
    }
}



?>