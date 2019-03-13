<?php
/**
 * This is a system wide global configuration file for "acl " authorization 
 *
 * 
 * 
 *
 */


/*
 * 
 * This is the analysis tree. It should have an main index for each Model that will
 * be exposed via the API
 * 
 * For the time being winAdmin and superAdmin have ALL permissions
 */    
$config['acl_tree_array'] = [
//    var $tree_array = [ 

    1 => ['category_id' => 10,  // WIN_MODEL,
         'category_name' => 'Linkedaccount',
         'parent_id' => 0,    
         'children' => [   
                        0 => [
                            'category_id' => 30,   //WIN_ACL_ROLE,
                            'category_name' =>  ['winAdmin', 'superAdmin'],                      
                            'parent_id' => 1,
                            'actions' => [],                                    
                            'children' => [
                                            0 => [ 
                                                'category_id' => 20,            //WIN_METHOD,       
                                                'category_name' => ['POST', 'DELETE', 'PATCH', 'PUT', 'GET_index', 'GET_view'],    // For the time being ALL POST's to this model are allowed
                                                'parent_id' => 2,
                                                'actions' =>  ['approve' => [],      
                                                              ],                                
                                                 ],
                                        ],        
                                ],
                        2 => [// THIS IS NOT READY, JUST THE SKELETON WAS COPIED FROM MODEL 'INVESTOR'
                            'category_id' => 30,   //WIN_ACL_ROLE,
                            'category_name' => ['investor'],                    // Must be an array
                            'parent_id' => 1,
                            'actions' => ['setListOfFields' => ['Linkedaccount', 'investor'],                                    
                                            ],  
                            'children' => [ 0 => [ 
                                                'category_id' => 20,            //WIN_METHOD,       
                                                'category_name' => 'GET_view',
                                                'parent_id' => 2,
                                                'actions' =>  ['checkOwner' => ['Linkedaccount', '$this->investorId', '$this->id'],       // The investor is the owner of the resource
                                                                'checkFields' => ['R', 'Linkedaccount',  'investor', '$this->listOfFields'], 
                                                              ],                                
                                                 ],
                                            1 => [ 
                                                'category_id' => 20,            //WIN_METHOD,       
                                                'category_name' => 'GET_index',
                                                'parent_id' => 2,   
                                                'actions' =>  [ 'addInvestorToSearchCriteria' => [ 
                                                                        'polling_userIdentification' => '$this->investorId',
                                                                        'pollingresource_status' => 'ACTIVE',
                                                                        'pollingresource_interval > ' => 0 ,                                  
                                                                        ],
                                                                'checkFields' => ['R', 'Linkedaccount',  'investor', '$this->listOfFields'],                                       
                                                        ],
                                            2 => [  
                                                'category_id' => 20,            //WIN_METHOD,                                                    
                                                'category_name' => 'PATCH',
                                                'parent_id' => 2,
                                                'actions' =>  ['checkOwner' => ['Linkedaccount', '$this->investorId', '$this->id'],  
                                                               'checkFields' => ['W', 'Linkedaccount',  'investor', '$this->listOfWriteFields'],
                                                ]
                                            ],
                                            3 => [  
                                                'category_id' => 20,            //WIN_METHOD,                                                    
                                                'category_name' => 'DELETE',
                                                'parent_id' => 2,
                                                'actions' =>  ['checkOwner' => ['Linkedaccount', '$this->investorId', '$this->id'],  
                                                               'approve' => []
                                                ]
                                            ]                                                
                                   ],
                            ],      
                ],  ],
        ],         
    2 => ['category_id' => 10,  // WIN_MODEL,
         'category_name' => 'Investor',
         'parent_id' => 0,
         'children' => [
                        1 => [
                            'category_id' => 30,   //WIN_ACL_ROLE,
                            'category_name' => ['superAdmin', 'winAdmin'],      // Must be an array
                            'parent_id' => 1,
                            'actions' => ['setListOfFields' => ['Investor', 'superAdmin'],                                                          
                                         ],
                            'children' => [ 0 => [  
                                                'category_id' => 20,            //WIN_METHOD,
                                                'category_name' => ['GET_view'],
                                                'parent_id' => 1,
                                                'actions' =>  ['checkFields' => ['R', 'Investor',  'superAdmin', '$this->listOfFields'],                    
                                                              ],
                                                 ],
                                            1 => [ 
                                                'category_id' => 20,            //WIN_METHOD,
                                                'category_name' => ['GET_index'],
                                                'parent_id' => 2,   
                                                'actions' =>  ['checkFields' => ['R', 'Investor',  'superAdmin', '$this->listOfFields'], 
                                                              ],                                              
                                                ],
                                            2 => [  
                                                'category_id' => 20,            //WIN_METHOD,                                               
                                                'category_name' => ['PATCH'],
                                                'parent_id' => 2,
                                                'actions' =>  ['checkFields' => ['W', 'Investor',  'superAdmin', '$this->listOfWriteFields'],                                            
                                                              ],
                                                ],
                                            3 => [  
                                                'category_id' => 20,            //WIN_METHOD,                                       
                                                'category_name' => ['DELETE'],
                                                'parent_id' => 2,
                                                'actions' =>  ['approve' => [],                                            
                                                    ]   
                                                ]
                                        ], 
                                ],
                             
                        2 => [
                            'category_id' => 30,   //WIN_ACL_ROLE,
                            'category_name' => ['investor'],                    // Must be an array
                            'parent_id' => 1,   
                            'children' => [ 0 => [ 
                                                'category_id' => 20,            //WIN_METHOD,       
                                                'category_name' => ['GET_view'],
                                                'parent_id' => 2,
                                                'actions' =>  [ 'setListOfFields' => ['Investor', 'investor'], 
                                                                'checkOwner' => ['Investor', '$this->investorId', '$this->request->params["id"]'],       // The investor is the owner of the resource
                                                                'checkFields' => ['R', 'Investor',  'investor', '$this->listOfFields']
                                                              ]
                                                                                           
                                                 ],
                                            2 => [  
                                                'category_id' => 20,            //WIN_METHOD,                                                    
                                                'category_name' => ['PATCH'],
                                                'parent_id' => 2,
                                                'actions' =>  ['checkOwner' => ['Investor', '$this->investorId', '$this->request->params["id"]'],       // The investor is the owner of the resource
                                                               'checkFields' => ['W', 'Investor',  'investor', '$this->listOfWriteFields'],
                                                ]
                                            ]
                                   ],
                            ],      
                ],         
       ],
    3 => ['category_id' => 10,  // WIN_MODEL,
         'category_name' => 'Email',
         'parent_id' => 0,
         'children' => [0 => [
                            'category_id' => 30,   //WIN_ACL_ROLE,
                            'category_name' =>  ['winAdmin', 'superAdmin'],                      
                            'parent_id' => 1,
                            'actions' => [],                                    
                            'children' => [
                                            0 => [ 
                                                'category_id' => 20,            //WIN_METHOD,       
                                                'category_name' => ['POST', 'DELETE', 'PATCH', 'PUT', 'GET_index', 'GET_view'],    // For the time being ALL POST's to this model are allowed
                                                'parent_id' => 2,
                                                'actions' =>  ['approve' => [],      
                                                              ],                                
                                                 ],
                                        ],        
                                ],  
                        2 => [// THIS IS NOT READY, JUST THE SKELETON WAS COPIED FROM MODEL 'INVESTOR'
                            'category_id' => 30,   //WIN_ACL_ROLE,
                            'category_name' => ['investor', 'nobody'],          // Must be an array
                            'parent_id' => 1,
                            'actions' => [],                                    
                            'children' => [ 1 => [ 
                                                'category_id' => 20,            //WIN_METHOD,       
                                                'category_name' => ['GET_index'],
                                                'parent_id' => 2,   
                                                'actions' =>  [ 'checkFields' => ['R', 'Email',  'investor', '$this->listOfFields'],                                       
                                                              ],
                                                 ],
                                            2 => [  
                                                'category_id' => 20,            //WIN_METHOD,                                                    
                                                'category_name' => ['POST'],
                                                'parent_id' => 2,
                                                'actions' =>  ['approve' => [],                                                                
                                                ]
                                            ]
                                   ],
                            ],      
                ],  
        ], 
    4 => ['category_id' => 10,  // WIN_MODEL,
         'category_name' => 'Pollingresource',
         'parent_id' => 0,
         'children' => [1 =>
                            [
                            'category_id' => 30,   //WIN_ACL_ROLE,
                            'category_name' => ['winAdmin', 'superAdmin'],      // Must be an array
                            'parent_id' => 1,   
                            'children' => [ 0 => [  
                                                'category_id' => 20,            //WIN_METHOD,
                                                'category_name' => ['GET_view'],
                                                'parent_id' => 1,
                                                'actions' =>  ['checkFields' => ['R', 'Pollingresource', 'winAdmin', '$this->listOfFields'],                                 
                                                              ],
                                                 ],
                                            1 => [ 
                                                'category_id' => 20,            //WIN_METHOD,
                                                'category_name' => ['GET_index'],
                                                'parent_id' => 2,  
                                                'actions' =>  ['checkFields' => ['R', 'Pollingresource',  'winAdmin', '$this->listOfFields']
                                                              ],                                              
                                                ],
                                            2 => [  
                                                'category_id' => 20,            //WIN_METHOD,                                           
                                                'category_name' => ['PATCH'],
                                                'parent_id' => 2,
                                                'actions' =>  ['checkFields' => ['W', 'Pollingresource', 'winAdmin', '$this->listOfFields'],                                            
                                                              ],
                                                ],
                                            3 => [  
                                                'category_id' => 20,            //WIN_METHOD,                                               
                                                'category_name' => ['DELETE'],
                                                'parent_id' => 2, 
                                                'actions' =>  ['approve' => [],                                            
                                                    ]   
                                                ]
                                        ], 
                                   ], 

                             
                        2 => [
                            'category_id' => 30,   //WIN_ACL_ROLE,
                            'category_name' => ['investor'],                    // Must be an array
                            'parent_id' => 1,
                            'actions' => [],  
                            'children' => [ 0 => [ 
                                                'category_id' => 20,            //WIN_METHOD,       
                                                'category_name' => ['GET_view'],
                                                'parent_id' => 2,
                                                'actions' =>  ['setListOfFields' => ['Pollingresource', 'investor'],
                                                               'checkOwner' => ['Pollingresource', '$this->investorId', '$this->request->params["id"]'],       // The investor is the owner of the resource
                                                               'checkFields' => ['R', 'Pollingresource',  'investor', '$this->listOfFields'], 
                                                              ],                                
                                                 ],
                                            1 => [ 
                                                'category_id' => 20,            //WIN_METHOD,       
                                                'category_name' => ['GET_index'],
                                                'parent_id' => 2,   
                                                'actions' =>  ['setListOfFields' => ['Pollingresource', 'investor'],
                                                               'addInvestorToSearchCriteria' => [                                  
                                                                       ['pollingresource_userIdentification' => '$this->investorId',
                                                                        'pollingresource_interval > ' => 0 , 
                                                                        'pollingresource_status' => 'ACTIVE'],
                                                                        
                                                                   ],
                                                               'checkFields' => ['R', 'Pollingresource',  'investor', '$this->listOfFields'],                                       
                                                        ],
                                                 ],
                                            2 => [  
                                                'category_id' => 20,            //WIN_METHOD,                                                    
                                                'category_name' => ['PATCH'],
                                                'parent_id' => 2,
                                                'actions' =>  ['setListOfFields' => ['Pollingresource', 'investor'],
                                                               'checkOwner' => ['Pollingresource', '$this->investorId', '$this->request->params["id"]'],  
                                                               'checkFields' => ['W', 'Pollingresource',  'investor', '$this->listOfWriteFields'],
                                                ]
                                            ],
                                            3 => [  
                                                'category_id' => 20,            //WIN_METHOD,                                                    
                                                'category_name' => ['DELETE'],
                                                'parent_id' => 2,
                                                'actions' =>  ['checkOwner' => ['Pollingresource', '$this->investorId', '$this->request->params["id"]'],  
                                                               'approve' => [],
                                                ]
                                            ]
                                   ],
                            ],      
                ], 
        
        ],
    5 => ['category_id' => 10,  // WIN_MODEL,
         'category_name' => 'Notification',
         'parent_id' => 0,
         'children' => [1 =>
                            [
                            'category_id' => 30,   //WIN_ACL_ROLE,
                            'category_name' => ['winAdmin', 'superAdmin'],      // Must be an array
                            'parent_id' => 1,   
                            'children' => [ 0 => [  
                                                'category_id' => 20,            //WIN_METHOD,
                                                'category_name' => ['GET_view'],
                                                'parent_id' => 1,
                                                'actions' =>  ['checkFields' => ['R', 'Notification', 'winAdmin', '$this->listOfFields'],  
                                                               'approve' => [], 
                                                              ],
                                                 ],
                                            1 => [ 
                                                'category_id' => 20,            //WIN_METHOD,
                                                'category_name' => ['GET_index'],
                                                'parent_id' => 2,  
                                                'actions' =>  ['checkFields' => ['R', 'Notification',  'winAdmin', '$this->listOfFields'],
                                                               'approve' => [], 
                                                              ],                                              
                                                ],
                                            2 => [  
                                                'category_id' => 20,            //WIN_METHOD,                                           
                                                'category_name' => ['PATCH'],
                                                'parent_id' => 2,
                                                'actions' =>  ['checkFields' => ['W', 'Notification', 'winAdmin', '$this->listOfFields'],                                            
                                                              ],
                                                ],
                                            3 => [  
                                                'category_id' => 20,            //WIN_METHOD,                                               
                                                'category_name' => ['DELETE'],
                                                'parent_id' => 2, 
                                                'actions' =>  ['approve' => [],                                            
                                                    ]   
                                                ]
                                        ], 
                                   ], 

                             
                        2 => [
                            'category_id' => 30,   //WIN_ACL_ROLE,
                            'category_name' => ['investor'],                    // Must be an array
                            'parent_id' => 1,
                            'actions' => ['setListOfFields' => ['Notification', 'investor'],                                    
                                            ],  
                            'children' => [ 0 => [ //STILL TO DO FOR NOTIFICATION
                                                'category_id' => 20,            //WIN_METHOD,       
                                                'category_name' => ['GET_view'],
                                                'parent_id' => 2,
                                                'actions' =>  ['checkOwner' => ['Notification', '$this->investorId', '$this->request->params["id"]'],       // The investor is the owner of the resource
                                                                'checkFields' => ['R', 'Notification',  'investor', '$this->listOfFields'], 
                                                              ],                                
                                                 ],
                                            1 => [ 
                                                'category_id' => 20,            //WIN_METHOD,       
                                                'category_name' => ['GET_index'],
                                                'parent_id' => 2,   
                                                'actions' =>  [ 'addInvestorToSearchCriteria' => [ 
                                                                        'investor_id' => '$this->investorId',
                                                                        'notification_status =>' => 'READY_FOR_VISUALIZATION',                            
                                                                        ],
                                                                'addInvestorToSearchCriteria' => [ 
                                                                        'investor_id' => '$this->investorId',
                                                                        'notification_status =>' => 'READ_BY_USER',                            
                                                                        ],
                                                                'checkFields' => ['R', 'Notification',  'investor', '$this->listOfFields'],  
                                                        ],
                                            2 => [  
                                                'category_id' => 20,            //WIN_METHOD,                                                    
                                                'category_name' => ['POST'],
                                                'parent_id' => 2,
                                                'actions' =>  ['checkOwner' => ['Notification', '$this->investorId', '$this->request->params["id"]'],  
                                                               'checkFields' => ['W', 'Notification',  'investor', '$this->listOfWriteFields'],
                                                               'approve' => [],
                                                ]
                                            ],
                                            3 => [  
                                                'category_id' => 20,            //WIN_METHOD,                                                    
                                                'category_name' => ['DELETE'],
                                                'parent_id' => 2,
                                                'actions' =>  ['checkOwner' => ['Notification', '$this->investorId', '$this->request->params["id"]'],  
                                                               'approve' => [],
                                                ]
                                            ]
                                   ],
                            ],      
                ],  ],
        ], 
    6 => ['category_id' => 10,  // WIN_MODEL,
         'category_name' => 'User',
         'parent_id' => 0,
         'children' => [  
                        0 => [
                            'category_id' => 30,   //WIN_ACL_ROLE,
                            'category_name' =>  ['winAdmin', 'superAdmin'],                      
                            'parent_id' => 1,
                            'actions' => ['approve'],                                    
                            'children' => [
                                            0 => [ 
                                                'category_id' => 20,            //WIN_METHOD,       
                                                'category_name' => ['POST', 'DELETE', 'PATCH', 'PUT', 'GET_index', 'GET_view'],    // For the time being ALL actions's to this model are allowed
                                                'parent_id' => 2,
                                                'actions' =>  ['approve' => [],      
                                                              ],                                
                                                 ],
                                        ],        
                                ],
                        1 => [
                            'category_id' => 30,   //WIN_ACL_ROLE,
                            'category_name' => ['investor'],                      
                            'parent_id' => 1,
                            'actions' => [],                                    
                            'children' => [
                                            0 => [ 
                                                'category_id' => 20,            //WIN_METHOD,       
                                                'category_name' => ['POST'],    // For the time being ALL PRIVATE POST's to this model are allowed
                                                'parent_id' => 2,
                                                'actions' =>  ['approve' => [],      
                                                              ],                                
                                                 ],
                                        ],        
                                ], 
                        2 => [
                            'category_id' => 30,   //WIN_ACL_ROLE,
                            'category_name' => ['nobody'],                      // This is an non-authenticated access
                            'parent_id' => 1,
                            'actions' => [],                                    
                            'children' => [
                                            0 => [ 
                                                'category_id' => 20,            //WIN_METHOD,       
                                                'category_name' => ['POST_public'],    // covers the login and precheck actions
                                                'parent_id' => 2,
                                                'actions' =>  ['approve' => [],      
                                                              ],                                
                                                 ],
                                        ],        
                                ], 
                        ],               
        ], 
    7 => ['category_id' => 10,  // WIN_MODEL, 
         'category_name' => 'Company',
         'parent_id' => 0,
         'children' => [   
                       0 => [
                            'category_id' => 30,   //WIN_ACL_ROLE,
                            'category_name' =>  ['winAdmin', 'superAdmin', 'investor'],                      
                            'parent_id' => 1,
                            'actions' => ['approve' => []],                                    
                            'children' => [
                                            0 => [ 
                                                'category_id' => 20,            //WIN_METHOD,       
                                                'category_name' => ['GET_index', 'GET_view'], 
                                                'parent_id' => 2,
                                                'actions' =>  ['approve' => [],      
                                                              ],                                
                                                 ],
                                        ],        
                                ],
                       ],            
        ],
    8 => ['category_id' => 10,  // WIN_MODEL,
         'category_name' => 'Dashboard',
         'parent_id' => 0,
         'children' => [   
                       0 => [
                            'category_id' => 30,   //WIN_ACL_ROLE,
                            'category_name' =>  ['winAdmin', 'superAdmin'],                      
                            'parent_id' => 1,
                            'actions' => [],                                    
                            'children' => [
                                            0 => [ 
                                                'category_id' => 20,            //WIN_METHOD,       
                                                'category_name' => ['POST', 'DELETE', 'PATCH', 'PUT', 'GET_index', 'GET_view'],    // For the time being ALL POST's to this model are allowed
                                                'parent_id' => 2,
                                                'actions' =>  ['approve' => [],      
                                                              ],                                
                                                 ],
                                        ],        
                                ],
                        2 => [// THIS IS NOT READY, need to be te
                            'category_id' => 30,   //WIN_ACL_ROLE,tested
                            'category_name' => ['investor'],                    // Must be an array
                            'parent_id' => 1, 
                            'actions' => ['setListOfFields' => ['Dashboard', 'investor'],                                    
                                            ],  
                            'children' => [ 0 => [ 
                                                'category_id' => 20,            //WIN_METHOD,       
                                                'category_name' => 'GET_view',
                                                'parent_id' => 2,
                                                'actions' =>  ['checkOwner' => ['Dashboard', '$this->investorId', '$this->request->params["id"]'],       // The investor is the owner of the resource
                                                              ],                                
                                                 ]
                                            ]
                             ],
                       ],            
        ], 
    9 => ['category_id' => 10,  // WIN_MODEL,
         'category_name' => 'Globaldashboard',
         'parent_id' => 0,
         'children' => [
                        0 => [
                            'category_id' => 30,   //WIN_ACL_ROLE,
                            'category_name' =>  ['winAdmin', 'superAdmin'],                      
                            'parent_id' => 1,
                            'actions' => [],                                    
                            'children' => [
                                            0 => [ 
                                                'category_id' => 20,            //WIN_METHOD,       
                                                'category_name' => ['POST', 'DELETE', 'PATCH', 'PUT', 'GET_index', 'GET_view'],    // For the time being ALL POST's to this model are allowed
                                                'parent_id' => 2,
                                                'actions' =>  ['approve' => [],      
                                                              ],                                
                                                 ],
                                        ],        
                                ],
                        2 => [// THIS IS NOT READY, STILL TO TEST
                            'category_id' => 30,   //WIN_ACL_ROLE,
                            'category_name' => ['investor'],                    // Must be an array
                            'parent_id' => 1,
                            'actions' => ['setListOfFields' => ['Globaldashboard', 'investor'],                                    
                                            ],  
                            'children' => [ 0 => [ 
                                                'category_id' => 20,            //WIN_METHOD,       
                                                'category_name' => 'GET_view',
                                                'parent_id' => 2,
                                                'actions' =>  ['checkOwner' => ['Globaldashboard', '$this->investorId', '$this->request->params["id"]'],       // The investor is the owner of the resource
                                                                               ],
                                                               'approve' => [], 
                                                 ],
                                            1 => [ 
                                                'category_id' => 20,            //WIN_METHOD,       
                                                'category_name' => 'GET_index',
                                                'parent_id' => 2,   
                                                'actions' =>  [ 'addInvestorToSearchCriteria' => [ 
                                                                        'investor_id' => '$this->investorId',                          
                                                                        ],
                                                                'approve' => [], 
                                                        ],
                                   ],
                            ],      
                ],  ],
        ], 
    10 => ['category_id' => 10,  // WIN_MODEL,
         'category_name' => 'Messages',
         'parent_id' => 0,
         'children' => [ 
                        0 => [
                            'category_id' => 30,   //WIN_ACL_ROLE,
                            'category_name' =>  ['winAdmin', 'superAdmin'],                      
                            'parent_id' => 1,
                            'actions' => [],                                    
                            'children' => [
                                            0 => [ 
                                                'category_id' => 20,            //WIN_METHOD,       
                                                'category_name' => ['POST', 'DELETE', 'PATCH', 'PUT', 'GET_index', 'GET_view'],    // For the time being ALL POST's to this model are allowed
                                                'parent_id' => 2,
                                                'actions' =>  ['approve' => [],      
                                                              ],                                
                                                 ],
                                        ],        
                                ],
                        2 => [// THIS IS NOT READY, JUST THE SKELETON WAS COPIED FROM MODEL 'INVESTOR'
                            'category_id' => 30,   //WIN_ACL_ROLE,
                            'category_name' => ['nobody'],                    // Must be an array
                            'parent_id' => 1,
                            'actions' => [],  
                            'children' => [ 2 => [  
                                                'category_id' => 20,            //WIN_METHOD,                                                    
                                                'category_name' => 'POST_public',
                                                'parent_id' => 2,
                                                'actions' =>  ['approve' => [],
                                                ]
                                            ]
                                   ],
                            ],      
                ],  
        ], 
    11 => ['category_id' => 10,  // WIN_MODEL,
         'category_name' => 'Fileuploads',
         'parent_id' => 0,
         'children' => [ 
                        0 => [
                            'category_id' => 30,   //WIN_ACL_ROLE,
                            'category_name' =>  ['winAdmin', 'superAdmin'],                      
                            'parent_id' => 1,
                            'actions' => [],                                    
                            'children' => [
                                            0 => [ 
                                                'category_id' => 20,            //WIN_METHOD,       
                                                'category_name' => ['POST', 'DELETE', 'PATCH', 'PUT', 'GET_index', 'GET_view'],    // For the time being ALL POST's to this model are allowed
                                                'parent_id' => 2,
                                                'actions' =>  ['approve' => [],      
                                                              ],                                
                                                 ],
                                        ],        
                                ],
                        2 => [// THIS IS NOT READY, JUST THE SKELETON WAS COPIED FROM MODEL 'INVESTOR'
                            'category_id' => 30,   //WIN_ACL_ROLE,
                            'category_name' => ['investor'],                    // Must be an array
                            'parent_id' => 1,
                            'actions' => ['setListOfFields' => ['Fileuploads', 'investor'],                                    
                                            ],  
                            'children' => [ 0 => [ 
                                                'category_id' => 20,            //WIN_METHOD,       
                                                'category_name' => 'GET_view',
                                                'parent_id' => 2,
                                                'actions' =>  ['checkOwner' => ['Fileuploads', '$this->investorId', '$this->request->params["id"]'],       // The investor is the owner of the resource
                                                               'checkFields' => ['R', 'Fileuploads',  'investor', '$this->listOfFields'], 
                                                              ],                                
                                                 ],
                                            1 => [ 
                                                'category_id' => 20,            //WIN_METHOD,       
                                                'category_name' => 'GET_index',
                                                'parent_id' => 2,   
                                                'actions' =>  [ 'addInvestorToSearchCriteria' => [ 
                                                                        'polling_userIdentification' => '$this->investorId',
                                                                        'pollingresource_status' => 'ACTIVE',
                                                                        'pollingresource_interval > ' => 0 ,                                  
                                                                        ],
                                                                'checkFields' => ['R', 'Fileuploads',  'investor', '$this->listOfFields'],                                       
                                                        ],
                                            2 => [  
                                                'category_id' => 20,            //WIN_METHOD,                                                    
                                                'category_name' => 'POST',
                                                'parent_id' => 2,
                                                'actions' =>  ['approve' => [],  
                                                ]
                                            ]
                                   ],
                            ],      
                ],  ],
        ], 
    12 => ['category_id' => 10,  // WIN_MODEL,
         'category_name' => 'Pubmessage',
         'parent_id' => 0,
         'children' => [   
                        0 => [
                            'category_id' => 30,   //WIN_ACL_ROLE,
                            'category_name' =>  ['winAdmin', 'superAdmin'],                      
                            'parent_id' => 1,
                            'actions' => [],                                    
                            'children' => [
                                            0 => [ 
                                                'category_id' => 20,            //WIN_METHOD,       
                                                'category_name' => ['POST', 'DELETE', 'PATCH', 'PUT', 'GET_index', 'GET_view'],    // For the time being ALL POST's to this model are allowed
                                                'parent_id' => 2,
                                                'actions' =>  ['approve' => [],      
                                                              ],                                
                                                 ],
                                        ],        
                                ],
                        2 => [// THIS IS NOT READY, JUST THE SKELETON WAS COPIED FROM MODEL 'INVESTOR'
                            'category_id' => 30,   //WIN_ACL_ROLE,
                            'category_name' => ['investor'],                    // Must be an array
                            'parent_id' => 1,
                            'actions' => ['setListOfFields' => ['Pubmessage', 'investor'],                                    
                                            ],  
                            'children' => [ 0 => [ 
                                                'category_id' => 20,            //WIN_METHOD,       
                                                'category_name' => 'GET_view',
                                                'parent_id' => 2,
                                                'actions' =>  ['checkOwner' => ['Pubmessage', '$this->investorId', '$this->request->params["id"]'],       // The investor is the owner of the resource
                                                                'checkFields' => ['R', 'Pubmessage',  'investor', '$this->listOfFields'], 
                                                              ],                                
                                                 ],
                                            1 => [ 
                                                'category_id' => 20,            //WIN_METHOD,       
                                                'category_name' => 'GET_index',
                                                'parent_id' => 2,   
                                                'actions' =>  [ 'addInvestorToSearchCriteria' => [ 
                                                                        'polling_userIdentification' => '$this->investorId',
                                                                        'pollingresource_status' => 'ACTIVE',
                                                                        'pollingresource_interval > ' => 0 ,                                  
                                                                        ],
                                                                'checkFields' => ['R', 'Pubmessage',  'investor', '$this->listOfFields'],                                       
                                                        ],
                                                ],
                                            1 => [ 
                                                'category_id' => 20,            //WIN_METHOD,       
                                                'category_name' => 'POST',
                                                'parent_id' => 2,   
                                                'actions' =>  [ 'addInvestorToSearchCriteria' => [ 
                                                                        'polling_userIdentification' => '$this->investorId',
                                                                        'pollingresource_status' => 'ACTIVE',
                                                                        'pollingresource_interval > ' => 0 ,                                  
                                                                        ],
                                                                'checkFields' => ['R', 'Pubmessage',  'investor', '$this->listOfFields'],                                       
                                                        ],
                                                ],
                                        ],      
                ],      ],
        ], 
    13 => ['category_id' => 10,  // WIN_MODEL,
         'category_name' => 'Companyglobals',
         'parent_id' => 0,
         'children' => [   
                        0 => [
                            'category_id' => 30,   //WIN_ACL_ROLE,
                            'category_name' =>  ['winAdmin', 'superAdmin'],                      
                            'parent_id' => 1,
                            'actions' => [],                                    
                            'children' => [
                                            0 => [ 
                                                'category_id' => 20,            //WIN_METHOD,       
                                                'category_name' => ['POST', 'DELETE', 'PATCH', 'PUT', 'GET_index', 'GET_view'],    // For the time being ALL POST's to this model are allowed
                                                'parent_id' => 2,
                                                'actions' =>  ['approve' => [],      
                                                              ],                                
                                                 ],
                                        ],        
                                ],
                        2 => [// THIS IS NOT READY, JUST THE SKELETON WAS COPIED FROM MODEL 'INVESTOR'
                            'category_id' => 30,   //WIN_ACL_ROLE,
                            'category_name' => ['investor'],                    // Must be an array
                            'parent_id' => 1,
                            'actions' => [],  
                            'children' => [ 0 => [ 
                                                'category_id' => 20,            //WIN_METHOD,       
                                                'category_name' => 'GET_view',
                                                'parent_id' => 2,
                                                'actions' =>  ['approve' => [], 
                                                              ],                                
                                                 ],
                                            1 => [ 
                                                'category_id' => 20,            //WIN_METHOD,       
                                                'category_name' => 'GET_index',
                                                'parent_id' => 2,   
                                                'actions' =>  [ 'approve' => [],                                     
                                                        ],
                                                ]
                                        ],
                            ],      
                ],  
        ], 
    ]; 
    
/*
 * The definitions of R/W permissions per Model and Role
 * 
 */    
   
$config['acl_referenceVariablePermissions'] = [
        "Investor" => [
                'investor' =>                   // Investor permissions for role 'investor'
                    ['id' => 'R', 
                    'investor_name' => 'RW', 
                    'investor_surname' => 'RW',
                    'investor_DNI' => 'RW',
                    'investor_dateOfBirth' => 'RW',
                    'investor_telephone' => 'RW',
                    'investor_email' => 'RW',
                    'investor_address1' => 'RW', 
                    'investor_address2' => 'RW', 
                    'investor_postCode' => 'RW',
                    'investor_city' => 'RW',
                    'investor_country' => 'RW', 
                    'investor_language' => 'RW',
                    'investor_links' => 'R',
                  ],
                'winAdmin' =>                    // Investor permissions for role winAdmin'. 
                    ['id' => 'R',              
                    'investor_identity' => 'R',
                    'investor_name' => 'RW', 
                    'investor_surname' => 'RW',
                    'investor_DNI' => 'RW',
                    'investor_dateOfBirth' => 'RW',
                    'investor_telephone' => 'RW',
                    'investor_email' => 'RW',
                    'investor_address1' => 'RW', 
                    'investor_address2' => 'RW', 
                    'investor_postCode' => 'RW',
                    'investor_city' => 'RW',
                    'investor_country' => 'RW', 
                    'investor_language' => 'RW',
                    'investor_accredited' => 'RW',            
                    'investor_links' => 'R',
                    'modified' => 'R',
                    'created' => 'R'          
                  ],
                'superAdmin'=>                   // Investor permissions for role 'superAdmin'
                    ['id' => 'R',                
                    'investor_identity' => 'R',
                    'investor_name' => 'RW', 
                    'investor_surname' => 'RW',
                    'investor_DNI' => 'RW',
                    'investor_dateOfBirth' => 'RW',
                    'investor_telephone' => 'RW',
                    'investor_email' => 'RW',
                    'investor_address1' => 'RW', 
                    'investor_address2' => 'RW', 
                    'investor_postCode' => 'RW',
                    'investor_city' => 'RW',
                    'investor_country' => 'RW', 
                    'investor_language' => 'RW',
                    'investor_accredited' => 'RW',            
                    'investor_links' => 'R',
                    'modified' => 'R',
                    'created' => 'R'         
                  ],            
        ],     
         
        "Pollingresource" => [
                'investor' =>                   // Pollingresource permissions for role 'investor'
                    ['id' => 'R', 
                    'pollingresource_userIdentification' => 'R',
                    'pollingresource_newValueExists' => 'R',
                    'pollingresource_interval' => 'R',
                    'pollingresource_type' => 'R',
            //      'pollingresource_value' => 'R'
            //      'pollingresource_resourceId' => 'R',
                    'pollingresource_links' => 'R'
                  ],
                'winAdmin' =>                    // Pollingresource permissions for role winAdmin'. 
                    ['id' => 'R',               
                    'pollingresource_userIdentification' => 'RW',
                    'pollingresource_newValueExists' => 'RW',
                    'pollingresource_interval' => 'RW',
                    'pollingresource_type' => 'RW',
                    'pollingresource_value' => 'RW',
                    'pollingresource_resourceId' => 'RW',
                    'pollingresource_links' => 'R',
                    'modified' => 'R',
                    'created' => 'R'           
                  ],
                'superAdmin'=>                   // Pollingresource permissions for role 'superAdmin'
                    ['id' => 'R',                
                    'pollingresource_userIdentification' => 'RW',
                    'pollingresource_newValueExists' => 'RW',
                    'pollingresource_interval' => 'RW',
                    'pollingresource_type' => 'RW',
                    'pollingresource_value' => 'RW',
                    'pollingresource_resourceId' => 'RW',
                    'pollingresource_links' => 'R',
                    'modified' => 'R',
                    'created' => 'R'           
                  ],
                ], 
  
        "Email" => [
                'investor' =>                    // Email permissions for role 'investor' and 'nobody'
                    [                            // No access permitted at all
                     'config' => 'R'
                  ],
                'winAdmin' =>                    // Email permissions for role winAdmin'. 
                    ['id' => 'R',               
                    'email_senderName' => 'RW',
                    'email_senderSurname' => 'RW',            
                    'email_senderEmail' => 'RW', 
                    'email_senderCompany' => 'RW',
                    'email_senderTelephone' => 'RW',
                    'email_senderJobTitle' => 'RW',            
                    'email_senderSubject' => 'RW',             
                    'email_senderText' => 'RW', 
                    'email_links' => 'R',
                    'modified' => 'R',
                    'created' => 'R'           
                  ],
                'superAdmin'=>                   // Email permissions for role 'superAdmin'
                    ['id' => 'R',                
                    'email_senderName' => 'RW',
                    'email_senderSurname' => 'RW',            
                    'email_senderEmail' => 'RW', 
                    'email_senderCompany' => 'RW',
                    'email_senderTelephone' => 'RW',
                    'email_senderJobTitle' => 'RW',            
                    'email_senderSubject' => 'RW',             
                    'email_senderText' => 'RW', 
                    'email_links' => 'R',
                    'modified' => 'R',
                    'created' => 'R'           
                  ],
                ],          
         
        "Company" => [
                'investor' =>                   // Company permissions for role 'investor'
                    ['id' => 'R', 
                    'company_name' => 'R',
                    'company_url' => 'R',
                    'company_country' => 'R', 
                    'company_countryName' => 'R', 
                    'company_privacyUrl' => 'R', 
                    'company_termsUrl' => 'R',
                    'company_logoGUID' => 'R',
                    'company_links' => 'R'
                  ],
                'winAdmin' =>                    // Company permissions for role winAdmin'. 
                    ['id' => 'R', 
                    'company_name' => 'RW',
                    'company_url' => 'RW',
                    'company_country' => 'RW', 
                    'company_countryName' => 'RW', 
                    'company_privacyUrl' => 'RW', 
                    'company_termsUrl' => 'RW',
                    'company_logoGUID' => 'RW',
                    'company_links' => 'R'
                  ],         
                'superAdmin'=>                   // Company permissions for role 'superAdmin'
                    ['id' => 'R', 
                    'company_name' => 'RW',
                    'company_url' => 'RW',
                    'company_country' => 'RW', 
                    'company_countryName' => 'RW', 
                    'company_privacyUrl' => 'RW', 
                    'company_termsUrl' => 'RW',
                    'company_logoGUID' => 'RW',
                    'company_links' => 'R'           
                  ],
                ],    
        "Dashboard" => [
                'investor' =>                   // Company permissions for role 'investor'
                    ['id' => 'R', 

                  ],
                'winAdmin' =>                    // Company permissions for role winAdmin'. 
                    ['id' => 'R', 

                  ],         
                'superAdmin'=>                   // Company permissions for role 'superAdmin'
                    ['id' => 'R', 
          
                  ],
                ],     
        "Globaldashboard" => [
                'investor' =>                   // Company permissions for role 'investor'
                    ['id' => 'R', 

                  ],
                'winAdmin' =>                    // Company permissions for role winAdmin'. 
                    ['id' => 'R', 

                  ],         
                'superAdmin'=>                   // Company permissions for role 'superAdmin'
                    ['id' => 'R', 
          
                  ],
                ], 
        "Messages" => [
                'investor' =>                   // Company permissions for role 'investor'
                    ['id' => 'R', 

                  ],
                'winAdmin' =>                    // Company permissions for role winAdmin'. 
                    ['id' => 'R', 

                  ],         
                'superAdmin'=>                   // Company permissions for role 'superAdmin'
                    ['id' => 'R', 
          
                  ],
                ],     
        "Pubmessage" => [
                'investor' =>                   // Company permissions for role 'investor'
                    ['id' => 'R', 

                  ],
                'winAdmin' =>                    // Company permissions for role winAdmin'. 
                    ['id' => 'R', 

                  ],         
                'superAdmin'=>                   // Company permissions for role 'superAdmin'
                    ['id' => 'R', 
          
                  ],
                ], 
        "Notification" => [
                'investor' =>                   // Notification permissions for role 'investor'
                    ['id' => 'R', 
    //                'notification_textId' => 'R', 
                    'notification_textShort' => 'R', 
                    'notification_textLong' => 'R', 
                    'notification_icon' => 'R', 
                    'notification_url' => 'R', 
                    'notification_type' => 'R', 
                    'notification_status' => 'R', 
                  ],
                'winAdmin' =>                    // Notification permissions for role winAdmin'. 
                    ['id' => 'R', 
                    'notification_textId' => 'R', 
                    'notification_textShort' => 'R', 
                    'notification_textLong' => 'R', 
                    'notification_icon' => 'R', 
                    'notification_publicationDateTime' => 'R', 
                    'notification_expiryDateTime' => 'R', 
                    'notification_url' => 'R', 
                    'notification_type' => 'R', 
                    'notification_status' => 'R', 
                    'created' => 'R', 
                    'modified' => 'R', 
                  ],         
                'superAdmin'=>                   // Notification permissions for role 'superAdmin'
                    ['id' => 'R', 
                    'notification_textId' => 'R', 
                    'notification_textShort' => 'R', 
                    'notification_textLong' => 'R', 
                    'notification_icon' => 'R', 
                    'notification_publicationDateTime' => 'R', 
                    'notification_expiryDateTime' => 'R', 
                    'notification_url' => 'R', 
                    'notification_type' => 'R', 
                    'notification_status' => 'R', 
                    'created' => 'R', 
                    'modified' => 'R',           
                  ],
                ],     
        "Companyglobal" => [
                'investor' =>                   // Company permissions for role 'investor'
                    ['id' => 'R', 

                  ],
                'winAdmin' =>                    // Company permissions for role winAdmin'. 
                    ['id' => 'R', 

                  ],         
                'superAdmin'=>                   // Company permissions for role 'superAdmin'
                    ['id' => 'R', 
         
                  ],
                ],
        "Fileupload" => [
                'investor' =>                   // Company permissions for role 'investor'
                    ['id' => 'R', 
                    'fileupload_typeOfDocument' => 'R',
                    'fileupload_originalFilename' => 'R',
                    'fileupload_uploadReference' => 'R', 
                    'fileupload_links' => 'R'
                  ],
                'winAdmin' =>                    // Company permissions for role winAdmin'. 
                    ['id' => 'R', 
                    'fileupload_typeOfDocument' => 'R',
                    'fileupload_originalFilename' => 'R',
                    'fileupload_uploadReference' => 'R', 
                    'fileupload_links' => 'R'
                  ],         
                'superAdmin'=>                   // Company permissions for role 'superAdmin'
                    ['id' => 'R', 
                    'fileupload_typeOfDocument' => 'R',
                    'fileupload_originalFilename' => 'R',
                    'fileupload_uploadReference' => 'R', 
                    'fileupload_links' => 'R'           
                  ],
                ],   
        "Linkedaccount" => [
                'investor' =>                   // Linkedaccount permissions for role 'investor'
                    ['id' => 'R', 
        //            'accountowner_id' => 'R',
                    'linkedaccount_accountDisplayName' => 'R',
                    'linkedaccount_currency' => 'R',
                    'linkedaccount_visualStatus' => 'R',
           //         'linkedaccount_companyId'
                  ],
                'winAdmin' =>                    // Linkedaccount permissions for role winAdmin'. 
                    ['id' => 'R', 
                    'accountowner_id' => 'R',
                    'linkedaccount_lastAccessed' => 'R',
                    'linkedaccount_linkingProcess' => 'R',
                    'linkedaccount_status' => 'R',
                    'linkedaccount_statusExtended' => 'R',
                    'linkedaccount_statusExtendedOld' => 'R',
                    'linkedaccount_alias' => 'R',
                    'linkedaccount_accountIdentity' => 'R',
                    'linkedaccount_accountDisplayName' => 'R',
                    'linkedaccount_isControlledBy' => 'R',
                    'linkedaccount_executionData' => 'R',
                    'linkedaccount_currency' => 'R',
                    'linkedaccount_visualStatus' => 'R',
                    'created' => 'R',
                    'modified' => 'R',
                  ],         
                'superAdmin'=>                   // Linkedaccount permissions for role 'superAdmin'
                    ['id' => 'R', 
                    'accountowner_id' => 'R',
                    'linkedaccount_lastAccessed' => 'R',
                    'linkedaccount_linkingProcess' => 'R',
                    'linkedaccount_status' => 'R',
                    'linkedaccount_statusExtended' => 'R',
                    'linkedaccount_statusExtendedOld' => 'R',
                    'linkedaccount_alias' => 'R',
                    'linkedaccount_accountIdentity' => 'R',
                    'linkedaccount_accountDisplayName' => 'R',
                    'linkedaccount_isControlledBy' => 'R',
                    'linkedaccount_executionData' => 'R',
                    'linkedaccount_currency' => 'R',
                    'linkedaccount_visualStatus' => 'R',
                    'created' => 'R',
                    'modified' => 'R',           
                  ],
                ],   
      ];  
