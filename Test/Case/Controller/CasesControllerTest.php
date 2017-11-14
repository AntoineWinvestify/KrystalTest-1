
<?php

class CasesControllerTest extends ControllerTestCase {

    public function testDivision() {
        $expected = "100";
        $actual = $this->testAction("/cases/testDivision");
        $this->assertEquals($expected, $actual[9]['investment']['division']);
    }

    public function testParserAnalyze() {
        $actual = $this->testAction("/cases/testParserAnalyze");
        $this->assertTrue(is_array($actual));
    }

    public function testParserConfig() {
        $expected = 1;
        $actual = $this->testAction("/cases/testParserConfig");
        $this->assertEquals($expected, $actual['offsetStart']);
    }

    public function testParserAnalyzeAndConfig() { //Offsets test
        $expectedOffsetStart = '888888-01';
        $expectedOffsetEnd = '888888-09';
        $actual = $this->testAction("/cases/testParserAnalyzeAndConfig");
        $this->assertEquals($expectedOffsetStart, $actual[0]['investment']['investment_loanId']);
        $this->assertEquals($expectedOffsetEnd, $actual[8]['investment']['investment_loanId']);
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
        $this->assertEquals($expected, $actual['85005-01']['investmentDate']);
    }

    public function testDate2() { //mm-dd-YYYY
        $expected = '2015-08-23';
        $expected2 = '2015-11-24';
        $actual = $this->testAction("/cases/testDate2");
        $this->assertEquals($expected, $actual[1]['investment']['investmentDate']);
        $this->assertEquals($expected2, $actual[2]['investment']['investmentDate']);
    }

    public function testDate3() {//  MM-DD-YYYY
        $expected = '2016-09-18';
        $actual = $this->testAction("/cases/testDate3");
        $this->assertEquals($expected, $actual[3]['investment']['investmentDate']);
    }

    public function testDate4() {//  YYYY.MM.DD
        $expected = '2015-12-17';
        $actual = $this->testAction("/cases/testDate4");
        $this->assertEquals($expected, $actual[4]['investment']['investmentDate']);
    }

    public function testDate5() {//  YYYY.mm.dd
        $expected = '2015-09-07';
        $actual = $this->testAction("/cases/testDate5");
        $this->assertEquals($expected, $actual[5]['investment']['investmentDate']);
    }

    public function testDate6() {//  YYYY.DD.MM
        $expected = '2015-01-13';
        $actual = $this->testAction("/cases/testDate6");
        $this->assertEquals($expected, $actual[6]['investment']['investmentDate']);
    }

    public function testDate7() {//  dd/mm/YYYY
        $expected = '2011-11-09';
        $expected2 = '2014-12-14';
        $actual = $this->testAction("/cases/testDate7");
        $this->assertEquals($expected, $actual[7]['investment']['investmentDate']);
        $this->assertEquals($expected2, $actual[8]['investment']['investmentDate']);
    }

    public function testDate8() { // DD/MM/YYYY
        $expected = '2015-11-05';
        $actual = $this->testAction("/cases/testDate8");
        $this->assertEquals($expected, $actual[9]['investment']['investmentDate']);
    }

    public function testDate9() {//  dd/mm/YYYY
        $expected = '2011-09-14';
        $expected2 = '2014-11-12';
        $actual = $this->testAction("/cases/testDate9");
        $this->assertEquals($expected, $actual[10]['investment']['investmentDate']);
        $this->assertEquals($expected2, $actual[11]['investment']['investmentDate']);
    }

    public function testDate10() { // DD/MM/YYYY
        $expected = '2015-05-15';
        $actual = $this->testAction("/cases/testDate10");
        $this->assertEquals($expected, $actual[12]['investment']['investmentDate']);
    }

    public function testCurrency() {
        $expected = array(1, 1, 1, 3, 3, 3, 2);
        $actual = $this->testAction("/cases/testCurrency");
        $this->assertEquals($expected[0], $actual[0]['investment']['currency']);
        $this->assertEquals($expected[1], $actual[1]['investment']['currency']);
        $this->assertEquals($expected[2], $actual[2]['investment']['currency']);
        $this->assertEquals($expected[3], $actual[3]['investment']['currency']);
        $this->assertEquals($expected[4], $actual[4]['investment']['currency']);
        $this->assertEquals($expected[5], $actual[5]['investment']['currency']);
        $this->assertEquals($expected[6], $actual[6]['investment']['currency']);
    }

    public function testAmount1() { // format 0,00453
        $expected = array('0.00454', '2400.5548', '2400.000995', '2545.442424', '2566.8778433868774', '2500.45214', "0.0");
        $actual = $this->testAction("/cases/testAmount1");
        $this->assertEquals($expected[0], $actual[0]['investment']['fullLoanAmount']);
        $this->assertEquals($expected[1], $actual[1]['investment']['fullLoanAmount']);
        $this->assertEquals($expected[2], $actual[2]['investment']['fullLoanAmount']);
        $this->assertEquals($expected[3], $actual[3]['investment']['fullLoanAmount']);
        $this->assertEquals($expected[4], $actual[4]['investment']['fullLoanAmount']);
        $this->assertEquals($expected[5], $actual[5]['investment']['fullLoanAmount']);
        $this->assertEquals($expected[6], $actual[14]['investment']['fullLoanAmount']);
        $this->assertEquals($expected[6], $actual[15]['investment']['fullLoanAmount']);
    }

    public function testAmount2() { // format 2.400,5548
        $expected = array("0.00000321", "2210000", "0.0002401", "24000", "0.0024", "0.0024");
        $actual = $this->testAction("/cases/testAmount2");
        $this->assertEquals($expected[0], $actual[6]['investment']['fullLoanAmount']);
        $this->assertEquals($expected[1], $actual[7]['investment']['fullLoanAmount']);
        $this->assertEquals($expected[2], $actual[8]['investment']['fullLoanAmount']);
        $this->assertEquals($expected[2], $actual[9]['investment']['fullLoanAmount']);
        $this->assertEquals($expected[3], $actual[10]['investment']['fullLoanAmount']);
        $this->assertEquals($expected[3], $actual[11]['investment']['fullLoanAmount']);
        $this->assertEquals($expected[4], $actual[12]['investment']['fullLoanAmount']);
        $this->assertEquals($expected[4], $actual[13]['investment']['fullLoanAmount']);
    }



    public function testExtracData() {
        $expected = 'n for th';
        $expected2 = 'bcd';
        $actual = $this->testAction("/cases/testExtracData");
        $this->assertEquals($expected, $actual[0]['investment']['test']);
        $this->assertEquals($expected2, $actual[1]['investment']['test']);
    }
   
    public function testExtracData2() {
        $expected = 'n for the revival of the next generation of solar cells that can function in a hostile environment$';
        $expected2 = 'bcdetheabcde';
        $actual = $this->testAction("/cases/testExtracData2");
        $this->assertEquals($expected, $actual[0]['investment']['test']);
        $this->assertEquals($expected2, $actual[1]['investment']['test']);
    }
    
    public function testHash() { //hash (Lithuania)
        $expected = 'd9051e0b77f8bb5521389618e70e2ada';
        $actual = $this->testAction("/cases/testHash");
        $this->assertEquals($expected, $actual[0]['investment']['hashCoutry']);
    }
    
    public function testRowData() { 
        $expected = 'Business Loan';
        $actual = $this->testAction("/cases/testRowData");
        $this->assertEquals($expected, $actual[12]['loanType']);
    }

   public function testTransactionDetail() { 
        $expected = array('investment_myInvestment', 'Unknown_income', 'Unknown_cost', 'Unknown_concept');
        
        $actual = $this->testAction("/cases/testTransactionDetail");
        $this->assertEquals($expected[0], $actual[0]['internalName']);
        $this->assertEquals($expected[1], $actual[14]['transactionDetail']);
        $this->assertEquals($expected[2], $actual[15]['transactionDetail']);
        $this->assertEquals($expected[3], $actual[16]['transactionDetail']);
    }
        
    
    public function testHtmlData(){
        $expected =  array(
            "amortizationtable_scheduledDate" => 2017-10-25,
            "amortizationtable_capitalAndInterestPayment" => 7726,
            "amortizationtable_capitalRepayment" => 6994,
            "amortizationtable_interest" => 732,
            "amortizationtable_paymentStatus" => CURRENT);
        
        $actual = $this->testAction("/cases/testHtmlData");
        $this->assertEquals($expected, $actual[0]);
    }
    
    public function  testDefault(){
        $expected = 2;
        $actual = $this->testAction("/cases/testDefault");
        foreach ($actual as $testDefault){
            $this->assertEquals($expected, $testDefault['investment_statusOfLoan']);
        }
    }


        public function tearDown() {
        parent::tearDown();
        unset($this->Hello);
    }
    
    

}
