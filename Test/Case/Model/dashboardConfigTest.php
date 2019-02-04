<?php

/*
 * Copyright (C) 2019 frodo
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

class dashboardConfigTest extends CakeTestCase {

    public function setUp() {
        parent::setUp();
        Configure::load('dashboardConfig.php', 'default');
        $this->pathVendor = Configure::read('winvestifyVendor');
    }

    public function testGraphDataConfig() {
        $dashboardConfig = Configure::read('Dashboard');
        $this->assertCount(2, $dashboardConfig, 'Config must have graphics and lists.');
        $this->assertArrayHasKey('graphics', $dashboardConfig, 'Config doesnt have graphics.');
        $this->assertArrayHasKey('lists', $dashboardConfig, 'Config doesnt have lists.');

        foreach ($dashboardConfig as $keyType => $valueType) {
            foreach ($dashboardConfig[$keyType] as $key => $config) {
                $this->assertCount(3, $config, 'Each config must have 3 subarrays, the first to find data, the second to format it, and the third to display the data. One is missing');

                $model = key($config[0]);
                $this->Model = ClassRegistry::init($model);
                $modelExists = class_exists($model);
                $this->assertTrue($modelExists, "Search class in $key not exists");
                if ($modelExists) {
                    $function = $config[0][$model];
                    $functionExists = method_exists($this->Model, $function);
                    $this->assertTrue($functionExists, "Search function in $key not exists");
                }

                $formatterClass = key($config[1]);
                include_once ($this->pathVendor . 'Classes' . DS . "$formatterClass.php");
                $this->formatter = new $formatterClass();
                $formatterClassExists = class_exists($formatterClass);
                $this->assertTrue($formatterClassExists, "formatter class in $key not exists");
                if ($formatterClassExists) {
                    $function = $config[1][$formatterClass];
                    $functionExists = method_exists($this->formatter, $function);
                    $this->assertTrue($functionExists, "Formatter function in $key not exists");
                }
                
                if(!empty($config[2]['xAxis'])){
                    $this->assertContains($config[2]['xAxis'], array('currency', '%', ''), "xAxis type not permited in $key");
                }
            }
        }
    }

    public function testMainDataConfig() {
        $dashboardConfig = Configure::read('DashboardMainData');
        foreach ($dashboardConfig as $key => $config) {
            $this->assertArrayHasKey('display_name', $config, "display_name not detected in $key");
            $this->assertArrayHasKey('tooltip', $config, "tooltip not detected in $key");
            $this->assertInternalType('int', $config['tooltip'], 'Tooltip not defined in constants or not an int');
            if (!empty($config['icon'])) {
                $this->assertArrayHasKey('graphLinksParams', $config, "$key have icon but not graphLinksParams");
            }
            if (!empty($config['graphLinksParams'])) {
                foreach ($config['graphLinksParams'] as $link) {
                    $this->assertArrayHasKey('link', $link, "link missing in $key");
                }
            }
            if (!empty($config['value'])) {
                $this->assertArrayHasKey('model', $config['value'], "model  not detected in value of $key");
                $this->assertArrayHasKey('field', $config['value'], "field not detected in value of $key");
                $model = $config['value']['model'];
                $this->Model = ClassRegistry::init($model);
                $modelExists = class_exists($model);
                $this->assertTrue($modelExists, "Search class in $key not exists");            
                if (!empty($config['value']['type'])) {
                    $this->assertContains($config['value']['type'], array('currency', '%'), "Value type not permited in $key");
                }
            }
        }
    }

}
