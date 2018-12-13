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

class TootipincompanyFixture extends CakeTestFixture {
    /**
     * table property
     *
     * @var string
     */
    public $table = 'tootipincompanies';

    /**
     * fields property
     *
     * @var array
     */
    public $fields = array(
        'id' => array('type' => 'integer', 'key' => 'primary'),
        'tooltip_id' => array('type' => 'integer', 'null' => false),
        'company_id' => array('type' => 'integer', 'null' => false),
        );

    /**
     * records property
     *
     * @var array
     */
    public $records = array(
        array('tooltip_id' => '1', 'company_id' => '25'),
        array('tooltip_id' => '2', 'company_id' => '25'),
        array('tooltip_id' => '9', 'company_id' => '24'),
        array('tooltip_id' => '3', 'company_id' => '24'),
        array('tooltip_id' => '5', 'company_id' => '25'),
        array('tooltip_id' => '6', 'company_id' => '24'),
        array('tooltip_id' => '6', 'company_id' => '25'),
        array('tooltip_id' => '8', 'company_id' => '24'),
        array('tooltip_id' => '8', 'company_id' => '25'),
        array('tooltip_id' => '10', 'company_id' => '24'),
        array('tooltip_id' => '14', 'company_id' => '24'),
        array('tooltip_id' => '15', 'company_id' => '24'),
        array('tooltip_id' => '16', 'company_id' => '24'),
        array('tooltip_id' => '17', 'company_id' => '24'),
        array('tooltip_id' => '14', 'company_id' => '25'),
        array('tooltip_id' => '15', 'company_id' => '25'),
        array('tooltip_id' => '16', 'company_id' => '25'),
        array('tooltip_id' => '17', 'company_id' => '25'),
        array('tooltip_id' => '18', 'company_id' => '24'),
        array('tooltip_id' => '19', 'company_id' => '24'),
        array('tooltip_id' => '18', 'company_id' => '25'),
        array('tooltip_id' => '19', 'company_id' => '25'),
    );

}
