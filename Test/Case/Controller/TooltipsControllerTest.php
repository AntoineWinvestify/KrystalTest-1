

<?php

class TooltipsControllerTest extends ControllerTestCase {

    public function testTooltip1() {
        $expected = "";
        $data = array(
            'Post' => array(
                'tooltipIdentifier' => array(15,16),
                'location' => 'en',
                'company' => 25
            )
        );

        $actual = $this->testAction("/tooltips/getTooltip", array('data' => $data, 'method' => 'get'));
        print_r($actual);
        $this->assertEquals($expected, $actual);
    }

    public function tearDown() {
        parent::tearDown();
        unset($this->Hello);
    }

}
