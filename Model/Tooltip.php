<?php

/**
  // +-----------------------------------------------------------------------+
  // | Copyright (C) 2017, https://www.winvestify.com                        |
  // +-----------------------------------------------------------------------+
  // | This file is free software; you can redistribute it and/or modify     |
  // | it under the terms of the GNU General Public License as published by  |
  // | the Free Software Foundation; either version 2 of the License, or     |
  // | (at your option) any later version.                                   |
  // | This file is distributed in the hope that it will be useful           |
  // | but WITHOUT ANY WARRANTY; without even the implied warranty of        |
  // | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the          |
  // | GNU General Public License for more details.                          |
  // +-----------------------------------------------------------------------+
  // | Author: Antoine de Poorter                                            |
  // +-----------------------------------------------------------------------+
 *
 *
 * @author Antoine de Poorter
 * @version 0.1
 * @date 2018-12-10		version 0.1
 * @package
 */
/*

  2018-12-10		version 0.1
  initial version





  Pending:


 */

class Tooltip extends AppModel {

    /*public $actsAs = array(
        'Translate' => array(
            'tooltip_text'
        )
    );*/
    public $hasMany = array(
        'Tooltipincompany' => array(
            'className' => 'Tooltipincompany',
            'foreignKey' => 'tooltip_id',
        )
    );
    var $uses = array('Tooltipincompany');

    /**
     * Search all the tooltips from a company
     * @param int $companyId pfp id to search the tooltips
     * @param strin $location
     * @return array
     */
    public function searchTooltipByCompany($companyId, $location) {

        $this->Tooltipincompany->Behaviors->load('Containable');

        $this->Tooltipincompany->contain('Tooltip');
        $this->Tooltipincompany->bindModel(
                array('belongsTo' => array(
                        'Tooltip' => array(
                            'className' => 'Tooltip',
                            'foreignKey' => 'tooltip_id'
                        )
                    )
                )
        );

        $companyFilter = array('company_id' => $companyId);
        $result = $this->Tooltipincompany->find('all', array('conditions' => $companyFilter,
            'fields' => 'id',
            'recursive' => -1,
        ));

        $translatedResult = $this->translateTooltips($result);
        return $translatedResult;
    }

    public function translateTooltips($tooltips) {

        foreach ($tooltips as $tooltip) {
            $idList[] = $tooltip['Tooltip']['id'];
        }
        
        $result = $this->find('all', array(
            'conditions' => array('Tooltip.id' => $idList)
        ));        
        
        return $result;
    }

    /**
     * Filter the tooltip from a company to obtain only the tooltips we want
     * @param array $tooltipIdentifier List of tooltip we want
     * @param array $tooltipArray List of all tooltip from a pfp
     * @return array
     */
    public function filterTooltipByIdentifier($tooltipIdentifier, $tooltipArray) {
        foreach ($tooltipArray as $key => $tooltip) {
            if (!in_array($tooltip['Tooltip']['tooltipidentifier_id'], $tooltipIdentifier)) {
                unset($tooltipArray[$key]);
            }
            unset($tooltipArray[$key]['Tooltipincompany']);
            unset($tooltipArray[$key]['Tooltip']['id']);
            unset($tooltipArray[$key]['Tooltip']['tooltip_type']);
        }

        return $tooltipArray;
    }

    /**
     * Get global tooltips that don't belong to a company
     * @param array $tooltipIdentifier List of tooltip we want
     * @param string $location
     * @return array
     */
    public function searchGlobalTooltip($tooltipIdentifier, $location) {
        //$this->locale = $location;

        $typeFilter = array('tooltip_type' => WIN_TOOLTIP_GLOBAL, 'tooltipidentifier_id' => $tooltipIdentifier);

        $result = $this->find('all', array('conditions' => $typeFilter,
            'recursive' => -1,
        ));

        foreach ($result as $key => $tooltip) {
            if (!in_array($tooltip['Tooltip']['tooltipidentifier_id'], $tooltipIdentifier)) {
                unset($result[$key]);
            }
            unset($result[$key]['Tooltip']['id']);
            unset($result[$key]['Tooltip']['tooltip_type']);
        }
        return $result;
    }

    /**
     * Get tooltips from model and format the array for the api
     * @param array $tooltipIdentifier
     * @param string $location
     * @param int $company
     */
    public function getTooltip($tooltipIdentifier, $location, $company = null) {
        $tooltipFormated = array();
        if (!empty($company)) {
            $tooltipsUnfiltered = $this->searchTooltipByCompany($company, $location);
            $tooltips = $this->filterTooltipByIdentifier($tooltipIdentifier, $tooltipsUnfiltered);
            
        }
        else {
            $tooltips = $this->searchGlobalTooltip($tooltipIdentifier, $location);
        }

        foreach($tooltips as $tooltip){
            $tooltipFormated[$tooltip['Tooltip']['tooltipidentifier_id']] = $tooltip['Tooltip']['tooltip_text'];
        }

        return $tooltipFormated;
    }
    
    
    
}
