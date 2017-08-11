<?php

App::uses('CakeEvent', 'Event');

class Structure extends AppModel {

    var $name = 'Structure';
    var $belongsTo = array(
        'Company' => array(
            'className' => 'Company',
            'foreignKey' => 'company_id',
        )
    );

    public function getStructure($companyId, $type) {
        echo $companyId . ' ' . $type;
        $structure = $this->find('first', array(
            'conditions' => array('company_id' => $companyId, 'structure_type' => $type),
            'order' => array('created DESC'),
            'recursive' => -1,
        ));
        return $structure;
    }

    public function saveStructure($data) {
        return $this->save($data);
    }

}
