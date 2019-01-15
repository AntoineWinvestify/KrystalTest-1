

<?php

App::uses('Tooltip', 'Model');

class TooltipTest extends CakeTestCase {

    public $fixtures = array('app.Tooltip', 'app.Tootipincompany');

    public function setUp() {
        parent::setUp();
        $this->Tooltip = ClassRegistry::init('Tooltip');
    }

    public function testTooltip1() {
        $expected1 = "Tooltip specific for Mintos. This can be a very extensive text which explains in fine detail all the specific things of the platform, like for instance f a platform caters for 'reserved funds'. It may also included a description of the formula that is used for calculating the Yield.";
        $actual = $this->Tooltip->getTooltip(array(15), 'es', 25);

        $this->assertEquals($expected1, $actual[15]);
    }

    public function testTooltip2() {
        $expected1 = "Tooltip specific for Mintos. This can be a very extensive text which explains in fine detail all the specific things of the platform, like for instance f a platform caters for 'reserved funds'. It may also included a description of the formula that is used for calculating the Yield.";
        $expected2 = 'Number of individual loans or assets that you currently own. The higher the sum, the better diversified your portfolio is.';

        $actual = $this->Tooltip->getTooltip(array(15, 16), 'es', 25);

        $this->assertEquals($expected1, $actual[15]);
        $this->assertEquals($expected2, $actual[16]);
    }

    public function testTooltip3() {
        $expected1 = "Tooltip specific for Mintos. This can be a very extensive text which explains in fine detail all the specific things of the platform, like for instance f a platform caters for 'reserved funds'. It may also included a description of the formula that is used for calculating the Yield.";
        $expected2 = 'Number of individual loans or assets that you currently own. The higher the sum, the better diversified your portfolio is.';
        $expected3 = 'This is not applicable to Mintos. Field will always show 0';

        $actual = $this->Tooltip->getTooltip(array(15, 16, 20), 'es', 25);

        $this->assertEquals($expected1, $actual[15]);
        $this->assertEquals($expected2, $actual[16]);
        $this->assertEquals($expected3, $actual[20]);
    }

    public function testTooltip4() {
        $expected1 = "This is your bankaccount number in IBAN format.Example: ES9121000418450200051332";

        $actual = $this->Tooltip->getTooltip(array(38), 'es');

        $this->assertEquals($expected1, $actual[38]);
    }

    public function testTooltip10() {
        $expected1 = array();

        $actual = $this->Tooltip->getTooltip(array(123), 'es');

        $this->assertEquals($expected1, $actual);
    }

    public function testTooltip12() {
        $expected1 = array();

        $actual = $this->Tooltip->getTooltip(array(123), 'es', 25);

        $this->assertEquals($expected1, $actual);
    }

    public function testTooltip13() {
        $expected1 = "Tooltip specific for Mintos. This can be a very extensive text which explains in fine detail all the specific things of the platform, like for instance f a platform caters for 'reserved funds'. It may also included a description of the formula that is used for calculating the Yield.";
        $expected2 = 'Number of individual loans or assets that you currently own. The higher the sum, the better diversified your portfolio is.';



        $actual = $this->Tooltip->getTooltip(array(15, 16, 34), 'es', 25);

        $this->assertEquals($expected1, $actual[15]);
        $this->assertEquals($expected2, $actual[16]);
    }
    
    public function testTooltip14() {
        $expected1 = array();

        $actual = $this->Tooltip->getTooltip(array(34), 'es', 15);

        $this->assertEquals($expected1, $actual);
    }

}
