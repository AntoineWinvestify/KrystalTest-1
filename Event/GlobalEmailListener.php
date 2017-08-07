<?php

/*
  // +-----------------------------------------------------------------------+
  // | Copyright (C) 2016, http://beyond-language-skills.com                 |
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
  //

  Used for listening to events
  Based on article "http://martinbean.co.uk/blog/2013/11/22/getting-to-grips-with-cakephps-events-system/'

 *
 * @author Antoine de Poorter
 * @version 0.3
 * @date 2017-03-05
 * @package
 *

  2016-11-14		version 0.1
  method "betaTesterConfirmationEmail" implemented													[OK, tested]


  2017-03-05		version 0.2
  methos "newUserCreatedEmail" implemented															[OK, tested]

  2017-03-29
  method "contactEmail" implemented

  PENDING:

  2017-05-15  Version 0.3                                                                     [OK, tested]
  Removed unused methods

  2017/07/04
 * pfp mailing
 */




App::uses('CakeEmail', 'Network/Email');
App::uses('Security', 'Utility');
App::uses('CakeEventListener', 'Event');

class GlobalEmailListener implements CakeEventListener {

    public function implementedEvents() {
        /*
          DEFINED EVENTS:
          newUserCreated			 			A new user has successfully registered
          SendContacMessage                   Somebody contacted use via ContactForm
         */

// Determine which events have been selected in the config file

        $allImplementedEvents = array(
            'newUserCreated' => 'newUserCreatedEmail',
            'sendContactMessage' => 'contactEmail',
            'checkMessage' => 'checkData',
            'billMailEvent' => 'billMail',
            'pfpMail' => 'newUserMail',
        );
        Configure::load('p2pGestor.php', 'default');
        $configuredEvents = Configure::read('event');
        foreach ($configuredEvents as $key => $value) {
            if ($value == true && !empty($allImplementedEvents[$key])) {
                $selectedEvents[$key] = $allImplementedEvents[$key];
            }
        }
        return ($selectedEvents);
    }




    /**
     *
     * Send a confirmation to the newly registered person on the platform
     *
     */
    public function newUserCreatedEmail(CakeEvent $event) {

        Configure::load('p2pGestor.php', 'default');
        $adminData = Configure::read('admin');

// First collect all the required data, which btw is not a lot
        $this->Investor = ClassRegistry::init('Investor');
        $this->Investor->recursive = -1;

        $resultInvestor = $this->Investor->findById($event->data['id']);

        try {
            $Email = new CakeEmail('smtp_Winvestify');
            $Email->from(array($adminData['genericEmailOriginator'] => 'WINVESTIFY'));
            $Email->to(array($resultInvestor['Investor']['investor_email'] => __("Client Winvestify")));
            $Email->subject(__("Thank you for registering at Winvestify"));
            $Email->template('newUser', 'standard_email_layout');
            $Email->emailFormat('html');
            $Email->send();
        } catch (Exception $e) {
            $infoString = __FILE__ . " " . __LINE__ . " Event: 'newUserCreated'. Caught email exception: " . $e->getMessage() . "\n";
            CakeLog::error($infoString);
        }

// TEMPORARY FIX: SEND A EMAIL TO MANUEL EVERYTIME WHEN A NEW USER HAS SUCCESFULLY REGISTERED. THIS IS TO BE REMOVED
// BY MAY 31 2017
        try {
            $Email = new CakeEmail('smtp_Winvestify');
            $Email->from(array($adminData['genericEmailOriginator'] => 'WINVESTIFY'));
            $Email->to(array('manuelmillan@winvestify.com' => __("Admin Winvestify")));
            $Email->subject(__("Registration of new user at Winvestify"));
            $Email->template('admin', 'standard_email_layout');
            $Email->viewVars(array('email' => $resultInvestor['Investor']['investor_email'],
                'telephone' => $resultInvestor['Investor']['investor_telephone'],
            ));
            $Email->emailFormat('html');
            $Email->send();
        } catch (Exception $e) {
            $infoString = __FILE__ . " " . __LINE__ . " Event: 'newUserCreated: admin email'. Caught email exception: " . $e->getMessage() . "\n";
            CakeLog::error($infoString);
        }
    }

    public function contactEmail(CakeEvent $event) {
        Configure::load('p2pGestor.php', 'default');
        $adminData = Configure::read('admin');
        // Send contact text to server admin
        try {
            $Email = new CakeEmail('smtp_Winvestify');
            $Email->from(array($adminData['genericEmailOriginator'] => 'WINVESTIFY'));
            $Email->to(array($adminData['systemAdmin'] => __("Admin")));
            $Email->subject($event->data['subject']);
            $Email->template('adminContactform', 'standard_email_layout');
            $Email->viewVars(array('name' => $event->data['name'],
                'text' => $event->data['text'],
                'subject' => $event->data['subject'],
                'email' => $event->data['email']));
            $Email->emailFormat('html');
            $Email->send();
        } catch (Exception $e) {
            $infoString = __FILE__ . " " . __LINE__ . " Event: 'SendContactMessage'. Caught email exception: " . $e->getMessage() . "\n";
            CakeLog::error($infoString);
            echo $infoString;
        }
        // Send contact text to user
        try {
            $Email = new CakeEmail('smtp_Winvestify');
            $Email->from(array($adminData['genericEmailOriginator'] => 'WINVESTIFY'));
            $Email->to($event->data['email']);
            $Email->subject($event->data['subject']);
            $Email->template('contactEmail', 'standard_email_layout');
            $Email->viewVars(array('name' => $event->data['name'],
                'text' => $event->data['text'],
                'subject' => $event->data['subject'],
                'email' => $event->data['email']));
            $Email->emailFormat('html');
            $Email->send();
        } catch (Exception $e) {
            $infoString = __FILE__ . " " . __LINE__ . " Event: 'SendContactMessage'. Caught email exception: " . $e->getMessage() . "\n";
            CakeLog::error($infoString);
            echo $infoString;
        }
    }

    /**
     * Mail to pfp admins after checking all investor data
     * 
     * @param CakeEvent $event
     */
    public function checkData(CakeEvent $event) {
        Configure::load('p2pGestor.php', 'default');
        $adminData = Configure::read('admin');
        // Send contact text to server admin
        try {
            $Email = new CakeEmail('smtp_Winvestify');
            $Email->from(array($adminData['genericEmailOriginator'] => 'WINVESTIFY'));
            $Email->to(array($adminData['winAdminCheck'] => __("Admin")));
            $Email->subject("Nuevo usuario ocr");
            $Email->template('winadminNewUserOcr', 'standard_email_layout');
            $Email->emailFormat('html');
            $Email->send();
        } catch (Exception $e) {
            $infoString = __FILE__ . " " . __LINE__ . " Event: 'SendContactMessage'. Caught email exception: " . $e->getMessage() . "\n";
            CakeLog::error($infoString);
            echo $infoString;
        }
    }

    /**
     * Mail to pfp admins after data checking 
     * 
     * @param CakeEvent $event
     */
    public function newUserMail(CakeEvent $event) {
        Configure::load('p2pGestor.php', 'default');
        $adminData = Configure::read('admin');

        foreach ($event->data as $mail) {

            // Send contact text to pfp admin
            try {
                $Email = new CakeEmail('smtp_Winvestify');
                $Email->from(array($adminData['genericEmailOriginator'] => 'WINVESTIFY'));
                $Email->to($mail['Adminpfp']['adminpfp_email']);
                $Email->subject(__("A new user want to register."));
                $Email->template('pfpadminNewUserOcr', 'standard_email_layout');
                $Email->emailFormat('html');
                $Email->send();
            } catch (Exception $e) {
                $infoString = __FILE__ . " " . __LINE__ . " Event: 'SendContactMessage'. Caught email exception: " . $e->getMessage() . "\n";
                CakeLog::error($infoString);
                echo $infoString;
            }
        }
    }

    /**
     * Mail to pfp admins after creating a bill
     * 
     * @param CakeEvent $event
     */
    public function billMail(CakeEvent $event) {
        Configure::load('p2pGestor.php', 'default');
        $adminData = Configure::read('admin');
        
        foreach ($event->data as $mail) {

            // Send contact text to pfp admin
            try {
                $Email = new CakeEmail('smtp_Winvestify');
                $Email->from(array($adminData['genericEmailOriginator'] => 'WINVESTIFY'));
                $Email->to($mail['Adminpfp']['adminpfp_email']);
                $Email->subject(__("You have a new bill."));
                $Email->template('pfpadminNewBill', 'standard_email_layout');
                $Email->emailFormat('html');
                $Email->send();
            } catch (Exception $e) {
                $infoString = __FILE__ . " " . __LINE__ . " Event: 'SendContactMessage'. Caught email exception: " . $e->getMessage() . "\n";
                CakeLog::error($infoString);
                echo $infoString;
            }
        }
    }

}
