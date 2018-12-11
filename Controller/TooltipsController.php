<?php

/*
 * +-----------------------------------------------------------------------+
 * | Copyright (C) 2016, http://beyond-language-skills.com                 |
 * +-----------------------------------------------------------------------+
 * | This file is free software; you can redistribute it and/or modify     |
 * | it under the terms of the GNU General Public License as published by  |
 * | the Free Software Foundation; either version 2 of the License, or     |
 * | (at your option) any later version.                                   |
 * | This file is distributed in the hope that it will be useful           |
 * | but WITHOUT ANY WARRANTY; without even the implied warranty of        |
 * | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the          |
 * | GNU General Public License for more details.                          |
 * +-----------------------------------------------------------------------+
 * | Author: Antoine de Poorter                                            |
 * +-----------------------------------------------------------------------+
 *
 *
 * @author Antoine de Poorter
 * @version 0.1
 * @date 2018-12-10
 * @package
 *

  2017-04-07	  version 2017_0.1
  function to copy a userdata photo to admin user.				[OK]

 */

class TooltipsController extends AppController {

    var $name = 'Tooltips';
    var $helpers = array();
    var $uses = array('Tooltip');
    var $error;

    function beforeFilter() {
        parent::beforeFilter();

        //$this->Security->requireAuth();
        //$this->Auth->allow(array('getTooltip'));
    }


    /**
     * Get tooltips from model and format the array for the api
     * @param array $tooltipIdentifier
     * @param string $location
     * @param int $company
     */
    public function getTooltip($tooltipIdentifier, $location, $company = null) {

        if (!empty($company)) {
            $tooltipsUnfiltered = $this->Tooltip->searchTooltipByCompany($company, $location);
            $tooltips = $this->Tooltip->filterTooltipByIdentifier($tooltipIdentifier, $tooltipsUnfiltered);
        }
        else {
            $tooltips = $this->Tooltip->searchGlobalTooltip($tooltipIdentifier, $location);
        }
        
        foreach($tooltips as $tooltip){
            $tooltipFormated[$tooltip['Tooltip']['tooltipidentifier_id']] = $tooltip['Tooltip']['tooltip_text'];
        }
        
        print_r($tooltipFormated);
        return $tooltipFormated;
    }

}
