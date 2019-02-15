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
 */    
$config['acl_tree_array'] = [
//    var $tree_array = [ 

    1 => ['category_id' => 10,  // WIN_MODEL,
         'category_name' => 'Linkedaccount',
         'parent_id' => 0,
         'children' => [0 =>
                            [
                            'category_id' => 30,   //WIN_ACL_ROLE,
                            'category_name' => 'investor',
                            'parent_id' => 2
                            ],     
                        ] 
        ],        
    2 => ['category_id' => 10,  // WIN_MODEL,
         'category_name' =>'Investor',
         'parent_id' => 0,
         'children' => [0 =>
                            [
                            'category_id' => 30,   //WIN_ACL_ROLE,
                            'category_name' => 'winAdmin',
                            'parent_id' => 1,   
                            'children' => [ 0 => [  
                                                'category_id' => 20,            //WIN_METHOD,
                                                'category_name' => 'GET_view',
                                                'parent_id' => 1,
                                                'actions' =>  ['checkFields' => ['R', 'Investor', 'winAdmin'],                                 
                                                              ],
                                                 ],
                                            1 => [ 
                                                'category_id' => 20,            //WIN_METHOD,
                                                'category_name' => 'GET_index',
                                                'parent_id' => 2,  
                                                'actions' =>  ['checkFields' => ['R', 'Investor',  'winAdmin']
                                                              ],                                              
                                                ],
                                            2 => [  
                                                'category_id' => 20,            //WIN_METHOD,                                           
                                                'category_name' => 'PATCH',
                                                'parent_id' => 2,
                                                'actions' =>  ['checkFields' => ['W', 'Investor', 'winAdmin'],                                            
                                                              ],
                                                ],
                                            3 => [  
                                                'category_id' => 20,            //WIN_METHOD,                                               
                                                'category_name' => 'DELETE',
                                                'parent_id' => 2, 
                                                'actions' =>  ['approve' => [],                                            
                                                    ]   
                                                ]
                                        ], 
                                   ], 
                        1 => [
                            'category_id' => 30,   //WIN_ACL_ROLE,
                            'category_name' => 'superAdmin',
                            'parent_id' => 1,
                            'actions' => ['getDefaultListOfFields' => ['Investor', 'superAdmin'],                                                          
                                         ],
                            'children' => [ 0 => [  
                                                'category_id' => 20,            //WIN_METHOD,
                                                'category_name' => 'GET_view',
                                                'parent_id' => 1,
                                                'actions' =>  ['checkFields' => ['R', 'Investor',  'superAdmin']                                 
                                                              ],
                                                 ],
                                            1 => [ 
                                                'category_id' => 20,            //WIN_METHOD,
                                                'category_name' => 'GET_index',
                                                'parent_id' => 2,   
                                                'actions' =>  ['checkFields' => ['R', 'Investor',  'superAdmin']
                                                              ],                                              
                                                ],
                                            2 => [  
                                                'category_id' => 20,            //WIN_METHOD,                                               
                                                'category_name' => 'PATCH',
                                                'parent_id' => 2,
                                                'actions' =>  ['checkFields' => ['W', 'Investor',  'superAdmin'],                                            
                                                              ],
                                                ],
                                            3 => [  
                                                'category_id' => 20,            //WIN_METHOD,                                       
                                                'category_name' => 'DELETE',
                                                'parent_id' => 2,
                                                'actions' =>  ['approve' => [],                                            
                                                    ]   
                                                ]
                                        ], 
                                ],
                             
                        2 => [
                            'category_id' => 30,   //WIN_ACL_ROLE,
                            'category_name' => 'investor',
                            'parent_id' => 1,
                            'actions' => ['getDefaultListOfFields' => ['Investor', 'investor'],
                                          'checkOwner' => ['Investor'],         // The investor is the owner of the resource
                                          'addInvestorToSearchCriteria' => [],
                                         ],
                            'children' => [ 0 => [ 
                                                'category_id' => 20,            //WIN_METHOD,       
                                                'category_name' => 'GET_view',
                                                'parent_id' => 2,
                                                'actions' =>  ['checkFields' => ['R', 'Investor',  'investor'], 
                                                              ],                                
                                                 ],
                                            1 => [ 
                                                'category_id' => 20,            //WIN_METHOD,       
                                                'category_name' => 'GET_index',
                                                'parent_id' => 2,   
                                                'actions' =>  ['checkFields' => ['R', 'Investor',  'investor']
                                                              ],                                              
                                                ],
                                            2 => [  
                                                'category_id' => 20,            //WIN_METHOD,                                                    
                                                'category_name' => 'PATCH',
                                                'parent_id' => 2,
                                                'actions' =>  ['checkFields' => ['W', 'Investor',  'investor'],                                            
                                                ]
                                            ]
                                   ],
                            ],
              ],         
        ],
    3 => ['category_id' => 10,  // WIN_MODEL,
         'category_name' => 'Email',
         'parent_id' => 0,
         'children' => [0 =>
                            [
                            'category_id' => 30,   //WIN_ACL_ROLE,
                            'category_name' => 'superAdmin',
                            'parent_id' => 2
                            ],     
                        ] 
        ],
    4 => ['category_id' => 10,  // WIN_MODEL,
         'category_name' => 'Pollingresource',
         'parent_id' => 0,
         'children' => [0 =>
                            [
                            'category_id' => 30,   //WIN_ACL_ROLE,
                            'category_name' => 'superAdmin',
                            'parent_id' => 3
                            ]  
                        ]
        ]   
    
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
                'investor' =>                    // Email permissions for role 'investor'
                    [                            // No access permitted at all

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

      ];  
