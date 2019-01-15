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

class TranslatetooltipFixture extends CakeTestFixture {

    /**
     * table property
     *
     * @var string
     */
    public $table = 'i18n';

    /**
     * fields property
     *
     * @var array
     */
    public $fields = array(
        'id' => array('type' => 'integer', 'key' => 'primary'),
        'locale' => array('type' => 'string', 'length' => 6, 'null' => false),
        'model' => array('type' => 'string', 'null' => false),
        'foreign_key' => array('type' => 'integer', 'null' => false),
        'field' => array('type' => 'string', 'null' => false),
        'content' => array('type' => 'text')
    );

    /**
     * records property
     *
     * @var array
     */
    public $records = array(
        array('locale' => 'es', 'model' => 'Tooltip', 'foreign_key' => 1, 'field' => 'tooltip_text', 'content' => 'Tooltip mintos'),
        array('locale' => 'es', 'model' => 'Tooltip', 'foreign_key' => 2, 'field' => 'tooltip_text', 'content' => 'Inversiones activas'),
        array('locale' => 'es', 'model' => 'Tooltip', 'foreign_key' => 3, 'field' => 'tooltip_text', 'content' => 'Tooltip finanzarel'),
        array('locale' => 'es', 'model' => 'Tooltip', 'foreign_key' => 4, 'field' => 'tooltip_text', 'content' => 'Tooltip global'),
    );

}
