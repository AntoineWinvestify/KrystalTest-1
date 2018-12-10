<?php
/**
 * ApiAdapter Component for CakePHP 2.10
 *
 * Provides methods to convert string constants, as used in the API, to integers 
 * which are used internally in the application and database. 
 * This is done for the JOSN of an incoming HTTP message and for the JSON to be 
 * sent in the HTTP response message.
 *
 * @author Antoine de Poorter
 * @version 0.1
 * @copyright Copyright 2018, Winvestify Asset Management S.L.
 * @license 
 * @package app.Controller.Component 
 */
App::uses('Component', 'Controller');
class ApiAdapterComponent extends Component {
    
    /**
     * Definitions for preparing the 'outgoing' JSON
     *
     * @access private
     * @var array
     */
    private $keywordsArrayOut = [
                    'linkedaccount_visual_state' => 
                        [ API_QUEUED => WIN_QUEUED , 
                          API_ANALYZING => WIN_ANALYZING,
                          API_MONITORED => WIN_MONITORED
                        ],
                    'linkedaccount_status' => 
                        [ API_LINKEDACCOUNT_STATUS_UNDEFINED => WIN_LINKEDACCOUNT_STATUS_UNDEFINED,
                          API_LINKEDACCOUNT_STATUS_NOT_ACTIVE => WIN_LINKEDACCOUNT_STATUS_NOT_ACTIVE,  
                          API_LINKEDACCOUNT_STATUS_ACTIVE => WIN_LINKEDACCOUNT_STATUS_ACTIVE
                        ],           
                    'metadata_type_of_document' => 
                        [ API_DNI_FRONT => WIN_DNI_FRONT,
                          API_DNI_BACK => WIN_DNI_BACK,
                          API_BANK_CERTIFICATE => WIN_BANK_CERTIFICATE
                        ],  
                    'polling_type' =>
                        [ API_NOTIFICATION_CHECK => WIN_NOTIFICATION_CHECK,
                          API_LINKEDACCOUNT_CHECK => WIN_LINKEDACCOUNT_CHECK,
                          API_PMESSAGE_CHECK => WIN_PMESSAGE_CHECK
                        ],
                    'service_status' =>   
                        [ API_SERVICE_STATE_NOT_ACTIVE => WIN_SERVICE_STATE_NOT_ACTIVE,
                          API_SERVICE_STATE_ACTIVE => WIN_SERVICE_STATE_ACTIVE,
                          API_SERVICE_STATE_SUSPENDED => WIN_SERVICE_STATE_SUSPENDED
                        ]          
                    ];


    /**
     * Definitions for dealing with the 'incoming' JSON
     *
     * @access private
     * @var array
     */    
    private $keywordsArrayIn = [
                    'linkedaccount_visual_state' => 
                        [ WIN_QUEUED => API_QUEUED, 
                          WIN_ANALYZING => API_ANALYZING,
                          WIN_MONITORED => API_MONITORED
                        ],
                    'linkedaccount_status' => 
                        [ WIN_LINKEDACCOUNT_STATUS_UNDEFINED => API_LINKEDACCOUNT_STATUS_UNDEFINED,
                          WIN_LINKEDACCOUNT_STATUS_NOT_ACTIVE => API_LINKEDACCOUNT_STATUS_NOT_ACTIVE, 
                          WIN_LINKEDACCOUNT_STATUS_ACTIVE => API_LINKEDACCOUNT_STATUS_ACTIVE
                        ],       
                    'metadata_type_of_document' => 
                        [ WIN_DNI_FRONT => API_DNI_FRONT,
                          WIN_DNI_BACK => API_DNI_BACK,
                          WIN_BANK_CERTIFICATE => API_BANK_CERTIFICATE],
                    'polling_type' =>
                        [ WIN_NOTIFICATION_CHECK => API_NOTIFICATION_CHECK,
                          WIN_LINKEDACCOUNT_CHECK => API_LINKEDACCOUNT_CHECK,
                          WIN_PMESSAGE_CHECK => API_PMESSAGE_CHECK
                        ],  
                    'service_status' =>   
                        [ WIN_SERVICE_STATE_NOT_ACTIVE => API_SERVICE_STATE_NOT_ACTIVE,
                          WIN_SERVICE_STATE_ACTIVE => API_SERVICE_STATE_ACTIVE,
                          WIN_SERVICE_STATE_SUSPENDED => API_SERVICE_STATE_SUSPENDED
                        ]     
                    ];
       
    /**
     * Recursive function for changing the values of an array.
     * 
     *  @param  $item The array value of the array element to be operated upon
     *  @param  $key The key of the array element to be operated upon
     *  @return -
     */ 
    private function changeArrayValueOut(&$item, $key) {

        if (array_key_exists($key, $this->keywordsArrayOut)) {
            $item = $this->keywordsArrayOut[$key][$item];
        }    
        return;
    }


    /**
     * Recursive function for changing the values of an array.
     * 
     *  @param  $item The array value of the array element to be operated upon
     *  @param  $key The key of the array element to be operated upon
     *  @return -
     */ 
    private function changeArrayValueIn(&$item, $key) {

        if (array_key_exists($key, $this->keywordsArrayIn)) {
            $item = $this->keywordsArrayIn[$key][$item];
        }    
        return;
    }


    /**
     * Changes the string value of fields to their corresponding internal 
     * integer value. This method operates directly on the provided array. 
     * 
     *  @param  array The array to operate upon
     *  @return boolean
     */
    public function normalizeIncomingJson(&$dataArray) {

        $functionArray = ['ApiAdapterComponent','changeArrayValueIn'];
        $result = array_walk_recursive($dataArray, $functionArray);
        return $result;        
    }
     
    
    /**
     * Changes the integer, internal, value of fields to their corresponding
     * string value. This method operates directly on the provided array. 
     * 
     *  @param  array The array to operate upon
     *  @return boolean
     */   
    public function normalizeOutgoingJson(&$dataArray) {

        $functionArray = ['ApiAdapterComponent','changeArrayValueOut'];
        $result = array_walk_recursive($dataArray, $functionArray);
        return $result;
    }      

}