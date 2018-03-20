<?php

/*
 * +-----------------------------------------------------------------------+
 * | Copyright (C) 2017, http://www.winvestify.com                         |
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
 * Functions for the AdminPFP role
 * 
 * 2017-06-14	  version 0.1
 * Initial version. 
 * All methods are "protected" using the "isAuthorized" function
 * 
 * added cronMoveToMLDatabase() method 
 * 
 * [2017-09-04] version 0.2
 * Added correct logout
 * 
 * Pending
 * Method "cronMoveToMLDatabase": fields 'userplatformglobaldata_reservedInvestments' and
 * 'userplatformglobaldata_finishedInvestments' are not yet available in the raw data
 * 
 * isChargeableEvent should also keep special conditions in mind, like NEVER charge the user, 
 * or charge only xx events/time-period/user, etc etc.
 *
 * [2017-07-15]      version 0.3
 * added methods cronMoveToMLDatabase(), writeArray, resetInvestmentArray() and resetInvestorsArray()
 * 
 * [2017-08-01]      version 0.4
 * moved methods "cronMoveToMLDatabase", "resetInvestmentArray" and "resetInvestorsArray" 
 * and "writeArray" to Shell
 * 
 * Pending
 * --
 * 
 */

App::uses('ClassRegistry', 'Utility');
App::uses('CakeEvent', 'Event');
App::uses('SimplePasswordHasher', 'Controller/Component/Auth');

class UsersController extends AdminpfpAppController {

    var $name = 'Users';
    var $helpers = array('Html', 'Form', 'Js', 'Session');
    var $uses = array('User', 'Adminpfp');
    var $components = array('Security');
    var $error;

    function beforeFilter() {

        parent::beforeFilter();

//	$this->Security->disabledFields = array('Participant.club'); // this excludes the club33 field from CSRF protection
        // as it is "dynamic" and would fail the CSRF test
//	$this->Security->requireSecure(	'login'	);
        $this->Security->csrfCheck = false;
        $this->Security->validatePost = false;
// Allow only the following actons.
//	$this->Security->requireAuth();
        $this->Auth->allow('login', 'loginAction', 'readMLDatabase');    // allow the actions without logon
//$this->Security->unlockedActions('login');
    }


    public function loginAction() {
        if ($this->Auth->login()) {   
            $id = $this->Session->read('Auth.User.adminpfp_id');
            $lang = $this->Session->read('Config.language');
            $this->Adminpfp->save(array('id' => $id, 'adminpfp_language' => $lang));
            $this->redirect($this->Auth->redirectUrl());
        }
        else {
            echo "User is not logged on<br>";
            // Inform the user why s/he could not login and offer "recover password" option
        }
    }

    /**
     *
     * 	Shows the login panel
     *
     */
    public function login() {
        if ($this->request->is('ajax')) {
            $this->layout = 'ajax';
            $this->disableCache();
        }
        else {
            $this->layout = 'Adminpfp.winvestify_adminpfp_login_layout';
        }

        $error = false;
        $this->set("error", $error);
    }

    public function logout() {
        $user = $this->Auth->user();  // get all the data of the authenticated user
        $event = new CakeEvent('Controller.User_logout', $this, array('data' => $user,
        ));
        $this->getEventManager()->dispatch($event);
        $this->Session->destroy();      // NOT NEEDED?
        $this->Session->delete('Auth');
        $this->Session->delete('Acl');
        $this->Session->delete('sectorsMenu');
        return $this->redirect($this->Auth->logout());
    }

    public function readMLDatabase() {
        $this->autoRender = false;
        Configure::write('debug', 2);


        Configure::load('p2pGestor.php', 'default');
        $serviceData = Configure::read('Tallyman');
        $limitDays = $serviceData['maxHistoryLengthDays'];
        $limitNumber = $serviceData['maxHistoryLengthNumber'];
        $cutoffDate = date("Y-m-d H:i:s", time() - $limitDays * 3600 * 7 * 24);

        $this->Investorglobaldata = ClassRegistry::init('Adminpfp.Investorglobaldata');

        $resultTallyman = $this->Investorglobaldata->find("all", $params = array('recursive' => 1,
            'conditions' => array(
                //           'userinvestmentdata_updateType' => SYSTEM_GENERATED,
                'created >' => $cutoffDate),
            'limit' => $limitNumber));

        $this->print_r2($resultTallyman);
    }

}
