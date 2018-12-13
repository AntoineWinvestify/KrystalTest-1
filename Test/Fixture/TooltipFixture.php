<?php

/*
 * Copyright (C) 2018 frodo
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

class TooltipFixture extends CakeTestFixture {

    /**
     * table property
     *
     * @var string
     */
    public $table = 'tootips';

    /**
     * fields property
     *
     * @var array
     */
    public $fields = array(
        'id' => array('type' => 'integer', 'key' => 'primary'),
        'tooltip_type' => array('type' => 'integer', 'null' => false),
        'tooltip_text' => array('type' => 'text', 'null' => false),
        'tooltipidentifier_id' => array('type' => 'int', 'null' => false)
    );

    /**
     * records property
     *
     * @var array
     */
    public $records = array(
        array('tooltip_type' => '2', 'tooltipidentifier_id' => '15', 'tooltip_text' => "Tooltip specific for Mintos. This can be a very extensive text which explains in fine detail all the specific things of the platform, like for instance f a platform caters for 'reserved funds'. It may also included a description of the formula that is used for calculating the Yield."),
        array('tooltip_type' => '1', 'tooltipidentifier_id' => '16', 'tooltip_text' => 'Number of individual loans or assets that you currently own. The higher the sum, the better diversified your portfolio is.'),
        array('tooltip_type' => '2', 'tooltipidentifier_id' => '15', 'tooltip_text' => 'tooltip only for Finanzarel'),
        array('tooltip_type' => '3', 'tooltipidentifier_id' => '38', 'tooltip_text' => 'This is your bankaccount number in IBAN format.Example: ES9121000418450200051332'),
        array('tooltip_type' => '2', 'tooltipidentifier_id' => '20', 'tooltip_text' => 'This is not applicable to Mintos. Field will always show 0'),
        array('tooltip_type' => '1', 'tooltipidentifier_id' => '18', 'tooltip_text' => 'The percentage of your "Total Volume", which is not invested in assets and therefore does not yield any interest currently.'),
        array('tooltip_type' => '3', 'tooltipidentifier_id' => '48', 'tooltip_text' => 'These are funds already allocated, but not yet invested'),
        array('tooltip_type' => '1', 'tooltipidentifier_id' => '19', 'tooltip_text' => 'Total nominal value of all assets held in your linked accounts'),
        array('tooltip_type' => '2', 'tooltipidentifier_id' => '16', 'tooltip_text' => 'The total number of investments that do not have outstanding = 0 '),
        array('tooltip_type' => '2', 'tooltipidentifier_id' => '20', 'tooltip_text' => 'These are (part of your) funds that the Platform used to make the investment. In the Finanzarel platform these funds are temporarily withdrawn when the investor participate in a auction. If the auction was succesfull the earlier mentioned funds are used to invest, hence they are converted to the investment amount, If the auction is not successfull then the money is returned to the use.'),
        array('tooltip_type' => '3', 'tooltipidentifier_id' => '39', 'tooltip_text' => 'This is your legal identification number. This may be the number as specified on your Identity Card.Example: 49583154N '),
        array('tooltip_type' => '3', 'tooltipidentifier_id' => '40', 'tooltip_text' => 'This is a mobile telephone number in international format.Example: +34666555444'),
        array('tooltip_type' => '3', 'tooltipidentifier_id' => '43', 'tooltip_text' => 'The legal identification number of your company. Typically this is a (international) VAT numberExample: B24958284F'),
        array('tooltip_type' => '1', 'tooltipidentifier_id' => '49', 'tooltip_text' => 'The date of the FIRST?? investment in this investment'),
        array('tooltip_type' => '1', 'tooltipidentifier_id' => '50', 'tooltip_text' => 'The TOTAL  amount invested'),
        array('tooltip_type' => '1', 'tooltipidentifier_id' => '51', 'tooltip_text' => 'The interest rate applied as provided by the PFP'),
        array('tooltip_type' => '1', 'tooltipidentifier_id' => '52', 'tooltip_text' => 'The progress of amortization in %. -> (number of instalments repaid * 100)/total number of instalments'),
        array('tooltip_type' => '1', 'tooltipidentifier_id' => '54', 'tooltip_text' => 'If date = 9999-12-31 means that we have passed the last date in the amortization table, but the loan has not yet been repayment yet (delay).If date is empty then it is a loan that will terminate TODAY'),
        array('tooltip_type' => '1', 'tooltipidentifier_id' => '55', 'tooltip_text' => 'The number of days of payment delay according to the amortization table. It shows the number of payment delays with respect to the date of the last regular update - 1.????'),      
    );

}
