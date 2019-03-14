  <?php
/**
 * Routes configuration
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different urls to chosen controllers and their actions (functions).
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Config
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Here, we are connecting '/' (base path) to controller called 'Pages',
 * its action called 'display', and we pass a param to select the view file
 * to use (in this case, /app/View/Pages/home.ctp)...
 */ 
		
/**
 * ...and connect the rest of 'Pages' controller's urls.
 */
	Router::connect('/pages/*', ['controller' => 'pages', 'action' => 'display']);
	Router::connect('/', ['controller' => 'marketplaces', 'action' => 'getGlobalMarketPlaceData']);
        
	Router::connect('/admin', ['plugin' => 'admin', 'controller' => 'users', 'action' => 'login']);
	Router::connect('/admin/:controller/:action/*', ['plugin' => 'Admin']);
        
	Router::connect('/adminpfp', ['plugin' => 'adminpfp', 'controller' => 'users', 'action' => 'login']);
        Router::connect('/adminpfp/:controller/:action/*', ['plugin' => 'Adminpfp']);
  
// Definition for routes for REST api version V1

/*
         Router::connect('/v1/:controller/:action/*',
            array(
                'prefix' => 'v1',
            )
        );    */    
        Router::parseExtensions('csv', 'json');

// Route used for generating pdf files
//Router::setExtensions(['json', 'xml', 'rss', 'pdf','csv']);

/* route added for intranet login */
//	Router::connect('/intranet/:action/*', array('controller' => 'users'));

/**
 * Load all plugin routes. See the CakePlugin documentation on
 * how to customize the loading of plugin routes.
 */
	CakePlugin::routes();

        
/*
 * 
 * A test for the REST-API
 * 
 */     
    //    Router::connect('/v1/:controller/:action/*', array('prefix' => 'v1', 'v1' => true));
    //    Router::connect('/:version/:controller/:action/*', array('prefix' => 'api', 'api' => true), array('version'=>'[0-9]+\.[0-9]+'));
        
     /* .json
    array('action' => 'v1_add', 'method' => 'POST', 'id' => false),
    array('action' => 'v1_edit', 'method' => 'PUT', 'id' => true),
     
    array('action' => 'v1_delete', 'method' => 'DELETE', 'id' => true),
    array('action' => 'v1_update', 'method' => 'POST', 'id' => true)
));       
    */  
// This applies to ALL controllers

    Router::connect("/:service/:version/companies/:id/*",
                    array("controller" => "companys",
                          "action" => "v1_view", 
                          "[method]" => "GET"),
                    array("id" => "[0-9]+",
                        "service" => "api",
                        "version" => '[0-9]+\.[0-9]+')
                    );      
    
    Router::connect("/:service/:version/companies/*",
                    array("controller" => "companys",
                          "action" => "v1_index", 
                          "[method]" => "GET"),
                    array("id" => "[0-9]+",
                        "service" => "api",
                        "version" => '[0-9]+\.[0-9]+')
                    );
 
    Router::connect("/:service/:version/:controller/:id",
                    array("action" => "v1_view", 
                        "[method]" => "GET"),
                    array("id" => "[0-9]+",
                        "service" => "api",
                        "version" => '[0-9]+\.[0-9]+')
                    );    
           
    Router::connect("/:service/:version/dashboards/:id/*",
                    array("controller" => "dashboards",
                        "action" => "v1_viewCustom", 
                            "[method]" => "GET"),
                    array("id" => "[0-9]+",
                        "service" => "api",
                        "version" => '[0-9]+\.[0-9]+')
                    ); 
    
    Router::connect("/:service/:version/globaldashboards/:id/*",
                    array("controller" => "globaldashboards",
                        "action" => "v1_viewCustom", 
                            "[method]" => "GET"),
                    array("id" => "[0-9]+",
                        "service" => "api",
                        "version" => '[0-9]+\.[0-9]+')
                    ); 
    
    Router::connect("/:service/:version/:controller/:id/*",
                array("action" => "v1_view", 
                        "[method]" => "GET",
                        "acl_action" => "GET_view"),
                    array("id" => "[0-9]+",
                        "service" => "api",
                        "version" => '[0-9]+\.[0-9]+')
                    );    
    
       
    Router::connect("/:service/:version/:controller/*",
                    array("action" => "v1_index", 
                        "[method]" => "GET",
                        "acl_action" => "GET_index"),
                    array("service" => "api", 
                          "version" => '[0-9]+\.[0-9]+' )
                    );  


    Router::connect("/:service/:version/:controller/:id",
                    array("action" => "v1_edit", 
                        "[method]" => "PATCH"),
                    array("id" => "[0-9]+",
                        "service" => "api",
                        "version" => '[0-9]+\.[0-9]+')
                    );   
 
    
    Router::connect("/:service/:version/:controller/login",
                    array("action" => "v1_login", 
                        "[method]" => "POST",
                        "acl_action" => "POST_public"),
                    array(
                        "service" => "api",
                        "version" => '[0-9]+\.[0-9]+')
                    ); 
    
    Router::connect("/:service/:version/:controller/logout",
                    array("action" => "v1_logout", 
                        "[method]" => "POST"),
                    array(
                        "service" => "api",
                        "version" => '[0-9]+\.[0-9]+')
                    ); 
    
    Router::connect("/:service/:version/:controller/pre-check",
                    array("action" => "v1_precheck", 
                        "[method]" => "POST"),
                    array(  
                        "service" => "api",
                        "version" => '[0-9]+\.[0-9]+')
                    );     
    Router::connect("/:service/:version/:controller/refresh-token",
                    array("action" => "v1_refreshToken", 
                        "[method]" => "POST"),
                    array(
                        "service" => "api",
                        "version" => '[0-9]+\.[0-9]+')
                    );     
    
    Router::connect("/:service/:version/:controller/:id/message-accepted",
                    array("action" => "v1_messageAccepted", 
                        "[method]" => "POST"),
                    array("id" => "[0-9]+",  
                        "service" => "api",
                        "version" => '[0-9]+\.[0-9]+')
                    );    
    
    Router::connect("/:service/:version/:controller/:id/message-confirmed",
                    array("action" => "v1_precheck", 
                        "[method]" => "POST"),
                    array("id" => "[0-9]+",  
                        "service" => "api",
                        "version" => '[0-9]+\.[0-9]+')
                    );        
    
    Router::connect("/:service/:version/:controller/:id/message-cancelled",
                    array("action" => "v1_messageCancelled", 
                        "[method]" => "POST"),
                    array("id" => "[0-9]+",  
                        "service" => "api",
                        "version" => '[0-9]+\.[0-9]+')
                    );    
    
    Router::connect("/:service/:version/:controller/:id/message-redirected",
                    array("action" => "v1_messageRedirected", 
                        "[method]" => "POST"),
                    array("id" => "[0-9]+",  
                        "service" => "api",
                        "version" => '[0-9]+\.[0-9]+')
                    );      
    
    Router::connect("/:service/:version/:controller/:id/check-message-code",
                    array("action" => "v1_checkMessageCode", 
                        "[method]" => "POST"),
                    array("id" => "[0-9]+",  
                        "service" => "api",
                        "version" => '[0-9]+\.[0-9]+')
                    );     
    
    Router::connect("/:service/:version/:controller/:id/send-message-code",
                    array("action" => "v1_sendMessageCode", 
                        "[method]" => "POST"),
                    array("id" => "[0-9]+",  
                        "service" => "api",
                        "version" => '[0-9]+\.[0-9]+')
                    );    
 
    Router::connect("/:service/:version/:controller/:id/send-message",
                    array("action" => "v1_sendMessage", 
                        "[method]" => "POST"),
                    array("id" => "[0-9]+",  
                        "service" => "api",
                        "version" => '[0-9]+\.[0-9]+')
                    );    

    Router::connect("/:service/:version/:controller/:id/notification_read",
                    array("action" => "v1_notificationRead", 
                        "[method]" => "POST"),
                    array("id" => "[0-9]+",  
                        "service" => "api",
                        "version" => '[0-9]+\.[0-9]+')
                    );   

    // This is the generic mapping for a POST
    Router::connect("/:service/:version/:controller/*",
                    array("action" => "v1_add", 
                        "[method]" => "POST"),
                    array("id" => "[0-9]+",  
                        "service" => "api",
                        "version" => '[0-9]+\.[0-9]+')
                    ); 
    
    
    Router::connect("/:service/:version/:controller/:id",
                    array("action" => "v1_delete", 
                        "[method]" => "DELETE"),
                    array("id" => "[0-9]+",
                        "service" => "api",
                        "version" => '[0-9]+\.[0-9]+')
                    );
    
 /* 

*/
//  Router::mapResources(array('tests')); 

    
/**
 * Load the CakePHP default routes. Only remove this if you do not want to use
 * the built-in default routes.
 */
	require CAKE . 'Config' . DS . 'routes.php';
	

    Router::parseExtensions();
