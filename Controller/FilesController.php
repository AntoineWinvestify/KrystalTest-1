<?php

/**
 * +--------------------------------------------------------------------------------------------+
 * | Copyright (C) 2016, http://www.winvestify.com                   	  	|
 * +--------------------------------------------------------------------------------------------+
 * | This file is free software; you can redistribute it and/or modify 		|
 * | it under the terms of the GNU General Public License as published by  |
 * | the Free Software Foundation; either version 2 of the License, or 	|
 * | (at your option) any later version.                                      		|
 * | This file is distributed in the hope that it will be useful   		    	|
 * | but WITHOUT ANY WARRANTY; without even the implied warranty of    		|
 * | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the      	|
 * | GNU General Public License for more details.        			              	|
 * +---------------------------------------------------------------------------------------------------------------+
 *
 *
 * @author
 * @version 0.1
 * @date 2016-10-25
 * @package
 *

  2016/29/2017 version 0.1
  function OneClickInvestorI, Save personal data in db                    [OK]
  function OneClickInvestorII Save selected companies                     [OK]
  function companyFilter      Company filter for platform selection panel [OK]
  function OneClickAdmin                                     [Not implemented]
  function OneClickCompany                                   [Not implemented]

  2017/6/06 version 0.1
  function upload                         [OK]

2017/6/08 version 0.2
 function delete                [ok]
 */
App::uses('CakeEvent', 'Event');

class filesController extends AppController {

    var $name = 'Files';
    var $helpers = array('Session');
    var $uses = array('Ocr', 'Company', 'Investor', 'File');
    var $error;

    function beforeFilter() {
        parent::beforeFilter(); // only call if the generic code for all the classes is required.
        $this->Security->requireAuth();
        $this->Auth->allow(); //allow these actions without login
    }

    function upload() {
        if (!$this->request->is('ajax')) {
            $result = false;
        } else {
            $this->layout = 'ajax';
            $this->disableCache();

            $data = $this->params['data']['Files'];
            $type = $data['info'];
            $id = $this->Investor->getInvestorId($this->Session->read('Auth.User.id'));
            $identity = $this->Investor->getInvestorIdentity($this->Session->read('Auth.User.id'));
            $result = $this->File->ocrFileSave($data, $identity, $id, $type);
            $this->set("fileInfo", $result);
        }
    }

    function delete() {
        if (!$this->request->is('ajax')) {
            $result = false;
        } else {
            $this->layout = 'ajax';
            $this->disableCache();

            $url = $this->request->data('url');
            $name = $this->request->data('name');
            $file_id = $this->request->data('id');
            $investor_id = $this->Investor->getInvestorId($this->Session->read('Auth.User.id'));

            print_r($url);
            print_r($name);
            print_r($file_id);
            print_r($investor_id);

            $result = $this->File->ocrFileDelete($url, $name, $file_id, $investor_id);

            $this->set("result", $result);
        }
    }

}
