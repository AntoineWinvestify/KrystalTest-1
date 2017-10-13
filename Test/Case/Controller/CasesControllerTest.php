
<?php

class CasesControllerTest extends ControllerTestCase {

    public function testDivision() {
        $expected = "52992.379690949";
        $actual = $this->testAction("/cases/testDivision");
        $this->assertEquals($expected, $actual['88031-01'][0]['investment']['division']);
    }

    public function testParserAnalyze() {
        $actual = $this->testAction("/cases/testParserAnalyze");
        $this->assertTrue(is_array($actual));
    }

    public function testParserConfig() {
        $expected = 'investment.investment_loanId';
        $actual = $this->testAction("/cases/testParserConfig");
        $this->assertEquals($expected, $actual['sortParameter']);
    }

    public function testParserAnalyzeAndConfig() {
        $actual = $this->testAction("/cases/testParserAnalyzeAndConfig");
        $this->assertTrue(is_array($actual));
    }

    public function testParserConfigFormat1() {
        $actual = $this->testAction("/cases/testParserConfigFormat1");
        $this->assertTrue(is_array($actual));
    }

    public function testParserConfigFormat2() {
        $actual = $this->testAction("/cases/testParserConfigFormat2");
        $this->assertTrue(is_array($actual));
    }

    public function testParserConfigFormat3() {
        $actual = $this->testAction("/cases/testParserConfigFormat3");
        $this->assertTrue(is_array($actual));
    }

    public function testParserConfigFormat4() {
        $actual = $this->testAction("/cases/testParserConfigFormat4");
        $this->assertTrue(is_array($actual));
    }

    public function testDate1() {// D-M-Y
        $expected = '2015-12-04';
        $actual = $this->testAction("/cases/testDate1");
        $this->assertEquals($expected, $actual['85005-01'][0]['investment']['investmentDate']);
    }

    public function testDate2() { //mm-dd-YYYY
        $expected = '2015-08-23';
        $expected2 = '2015-11-24';
        $actual = $this->testAction("/cases/testDate2");
        $this->assertEquals($expected, $actual['88031-01'][0]['investment']['investmentDate']);
        $this->assertEquals($expected2, $actual['1655778-01'][0]['investment']['investmentDate']);
    }

    public function testDate3() {//  MM-DD-YYYY
        $expected = '2016-09-18';
        $actual = $this->testAction("/cases/testDate3");
        $this->assertEquals($expected, $actual['888888-01'][0]['investment']['investmentDate']);
    }

    public function testDate4() {//  YYYY.MM.DD
        $expected = '2015-12-17';
        $actual = $this->testAction("/cases/testDate4");
        $this->assertEquals($expected, $actual['888888-02'][0]['investment']['investmentDate']);
    }

    public function testDate5() {//  YYYY.mm.dd
        $expected = '2015-09-07';
        $actual = $this->testAction("/cases/testDate5");
        $this->assertEquals($expected, $actual['888888-03'][0]['investment']['investmentDate']);
    }

    public function testDate6() {//  YYYY.DD.MM
        $expected = '2015-01-13';
        $actual = $this->testAction("/cases/testDate6");
        $this->assertEquals($expected, $actual['888888-04'][0]['investment']['investmentDate']);
    }

    public function testDate7() {//  dd/mm/YYYY
        $expected = '2011-11-09';
        $expected2 = '2014-12-14';
        $actual = $this->testAction("/cases/testDate7");
        $this->assertEquals($expected, $actual['888888-05'][0]['investment']['investmentDate']);
        $this->assertEquals($expected2, $actual['888888-06'][0]['investment']['investmentDate']);
    }

    public function testDate8() { // DD/MM/YYYY
        $expected = '2015-11-05';
        $actual = $this->testAction("/cases/testDate8");
        $this->assertEquals($expected, $actual['888888-07'][0]['investment']['investmentDate']);
    }

    public function testDate9() {//  dd/mm/YYYY
        $expected = '2011-09-14';
        $expected2 = '2014-11-12';
        $actual = $this->testAction("/cases/testDate9");
        $this->assertEquals($expected, $actual['888888-08'][0]['investment']['investmentDate']);
        $this->assertEquals($expected2, $actual['888888-09'][0]['investment']['investmentDate']);
    }

    public function testDate10() { // DD/MM/YYYY
        $expected = '2015-05-15';
        $actual = $this->testAction("/cases/testDate10");
        $this->assertEquals($expected, $actual['888888-10'][0]['investment']['investmentDate']);
    }

    public function testCurrency() {
        $expected = array(1, 1, 1, 3, 3, 3, 2);
        $actual = $this->testAction("/cases/testCurrency");
        $this->assertEquals($expected[0], $actual['85005-01'][0]['investment']['currency']);
        $this->assertEquals($expected[1], $actual['88031-01'][0]['investment']['currency']);
        $this->assertEquals($expected[2], $actual['1655778-01'][0]['investment']['currency']);
        $this->assertEquals($expected[3], $actual['888888-01'][0]['investment']['currency']);
        $this->assertEquals($expected[4], $actual['888888-02'][0]['investment']['currency']);
        $this->assertEquals($expected[5], $actual['888888-03'][0]['investment']['currency']);
        $this->assertEquals($expected[6], $actual['888888-04'][0]['investment']['currency']);
    }

    public function testAmount1() { // format 0,00453
        $expected = '00045300000000000';
        $actual = $this->testAction("/cases/testAmount1");
        $this->assertEquals($expected, $actual['85005-01'][0]['investment']['fullLoanAmount']);
    }

    public function testAmount2() { // format 2.400,5548
        $expected = '240055480';
        $actual = $this->testAction("/cases/testAmount2");
        $this->assertEquals($expected, $actual['88031-01'][0]['investment']['fullLoanAmount']);
    }

    public function testAmount3() { // format €24005,000995
        $expected = '240050009950';
        $actual = $this->testAction("/cases/testAmount3");
        $this->assertEquals($expected, $actual['1655778-01'][0]['investment']['fullLoanAmount']);
    }

    public function testAmount4() {  // format €2.545,442424
        $expected = '25454424240';
        $actual = $this->testAction("/cases/testAmount4");
        $this->assertEquals($expected, $actual['888888-01'][0]['investment']['fullLoanAmount']);
    }

    public function testAmount5() { // format 2.566,8778433868774
        $expected = '256687784338687740';
        $actual = $this->testAction("/cases/testAmount5");
        $this->assertEquals($expected, $actual['888888-02'][0]['investment']['fullLoanAmount']);
    }

    public function testAmount6() { // format 2500,45214€
        $expected = '2500452140';
        $actual = $this->testAction("/cases/testAmount6");
        $this->assertEquals($expected, $actual['888888-03'][0]['investment']['fullLoanAmount']);
    }

    public function testAmount7() { // format 2500,45214€
        $expected = '00000032100000000';
        $expected2 = '2210000';
        $actual = $this->testAction("/cases/testAmount7");
        $this->assertEquals($expected, $actual['888888-04'][0]['investment']['fullLoanAmount']);
        $this->assertEquals($expected2, $actual['888888-05'][0]['investment']['fullLoanAmount']);
    }

    public function testExtracData() {
        $expected = 'n for th';
        $expected2 = 'bcd';
        $actual = $this->testAction("/cases/testExtracData");
        $this->assertEquals($expected, $actual['85005-01'][0]['investment']['test']);
        $this->assertEquals($expected2, $actual['88031-01'][0]['investment']['test']);
    }

    public function testHash() { //hash (Lithuania)
        $expected = 'd9051e0b77f8bb5521389618e70e2ada';
        $actual = $this->testAction("/cases/testHash");
        $this->assertEquals($expected, $actual['85005-01'][0]['investment']['hashCoutry']);
    }
    
   /* public function testRowData(){
        
        
        
    }*/
    

    public function tearDown() {
        parent::tearDown();
        unset($this->Hello);
    }

}
