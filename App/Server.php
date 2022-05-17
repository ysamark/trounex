<?php

namespace App;

use Closure;
use App\Utils\PageExceptions\Error;
use App\Controllers\BaseController;
use Symfony\Component\HttpFoundation\Request;

class Server {
  /**
   * @var string
   */
  private static $viewPath = null;

  /**
   * @var array
   */
  private static $pathPrefix = [
    'pattern' => '/^(\/+)/',
    'text' => '/'
  ];

  /**
   * @var string
   */
  private static $viewLayout;

  /**
   * @var array
   */
  private static $defaultHandlerArguments;

  /**
   * @var BaseController
   */
  private static $viewGlobalContext;
  
  /**
   * run the application server to start serving the pages
   */
  public static function Run () {
    $requestUrl = $_SERVER ['REQUEST_URI'];

    $requestUrlSlices = preg_split ('/\?+/', $requestUrl);

    $viewsPath = dirname (__DIR__) . '/views';
    $routePath = trim (preg_replace ('/^(\/|\\\)+/', '', $requestUrlSlices [0]));
    $routePath = trim (preg_replace ('/(\/|\\\)+$/', '', $routePath));

    $routePath = preg_replace (self::$pathPrefix ['pattern'], '', $routePath);

    $routeViewPathAlternates = [
      "$viewsPath/{$routePath}.php",
      "$viewsPath/$routePath/index.php"
    ];

    $include = function ($__view) {
      $vars = get_object_vars ($this);

      if (BaseController::isControllerInstance ($this)) {
        $vars = $this->getProps ();  
      }

      foreach ($vars as $key => $var) {
        $varName = is_array ($var) ? $var ['name'] : $key;
        $value = is_array ($var) ? $var ['value'] : $vars [$key];

        if (preg_match ('/^([a-zA-Z0-9_]+)$/', $varName)) {
          $$varName = $value;
        }
      }
      
      include ($__view ['path']);
    };

    $actionMethod = 'handler';

    if ($apiSourcePath = self::getApiRouteSourceFile ($routePath)) {
      $apiSourceClassPath = join ('\\', [
        preg_replace ('/\/+/', '\\', $routePath)
      ]);

      self::setViewPath (realpath ($apiSourcePath));

      include_once $apiSourcePath;

      if (class_exists ($apiSourceClassPath)) {
        $api = new $apiSourceClassPath ();

        if (method_exists ($api, $actionMethod)) {
          self::beforeAPIHandler ();

          call_user_func_array ([$api, $actionMethod], self::defaultHandlerArguments ());

          exit (0);
        }
      }

      Error::Throw404 ();
    }

    foreach ($routeViewPathAlternates as $routeViewPath) {
      if (is_file ($routeViewPath)) {
        self::setViewPath (realpath ($routeViewPath));
        
        self::beforeRender ();

        call_user_func_array (self::lambda ($include), [['path' => self::mainLayoutView ()]]);

        exit (0);
      }
    }

    if ($publicFilePath = self::publicFileExists ($routePath)) {
      return self::serveStaticFile ($publicFilePath);
    }

    $dynamicRoutesPaths = Router::GetRoutesPath ($viewsPath);

    $routeViewPathBase = preg_replace ('/[\/\\\]/', DIRECTORY_SEPARATOR, "$viewsPath{$routePath}");

    $routeViewPaths = [
      $routeViewPathBase . DIRECTORY_SEPARATOR . 'index.php',
      $routeViewPathBase . '.php'
    ];
    
    foreach ($dynamicRoutesPaths as $route) {
      $routeRe = $route ['routeRe'];

      foreach ($routeViewPaths as $index => $routeViewPath) {
        if (@preg_match ($routeRe, $routeViewPath, $match)) {        
          self::setViewPath (realpath ($route ['originalFilePath']));

          if ($index == 0 && !is_file ($routeViewPath)) {
            continue;
          }
  
          $routeParamKeys = $route ['match'][1];
          $routeParamValues = array_slice ($match, 2, count ($match));
  
          Router::EvaluateRouteParams ($routeParamKeys, $routeParamValues);
  
          if (self::isAPIRoutePath ($routePath)) {
            self::setViewPath (realpath ($route ['originalFilePath']));
            $api = require (realpath ($route ['originalFilePath']));
  
            self::beforeAPIHandler ();

            $action = param ('_action');
            $actionMethod = is_string ($action) && !empty ($action) ? $action : 'handler';
  
            if (is_object ($api) && method_exists ($api, $actionMethod)) {
              return call_user_func_array ([$api, $actionMethod], self::defaultHandlerArguments ());
            }

            Error::Throw404 ();
          } else {
            self::beforeRender ();

            return call_user_func_array (self::lambda ($include), [['path' => self::mainLayoutView ()]]);
          }
  
          exit (0);
        }
      }
    }

    exit ("Page Not Found => " . $routeViewPath);
  }

  protected static function getApiRouteSourceFile ($routePath) {
    $viewsPath = dirname (__DIR__) . '/views';

    if (self::isAPIRoutePath ($routePath)) {
      $routeApiPathAlternates = [
        "$viewsPath/{$routePath}.php",
        "$viewsPath/$routePath/index.php"
      ];
  
      foreach ($routeApiPathAlternates as $routeApiPath) {
        if (is_file ($routeApiPath)) {
          return $routeApiPath;
        }
      }
    }
  }

  protected static function beforeAPIHandler () {
    self::beforeRenderOrAPIHandler ();
    $_SESSION ['_post'] = $_POST;
  }

  protected static function beforeRender () {
    self::beforeRenderOrAPIHandler ();
    register_shutdown_function ('App\Utils\ShutDownFunction');

    $viewPath = self::GetViewPath ();

    $viewControllerPath = join ('', [
      preg_replace ('/(\.php)$/', '.controller.php', $viewPath)
    ]);

    if (is_file ($viewControllerPath)) {
      $viewControllerInstance = require ($viewControllerPath);

      if (is_callable ($viewControllerInstance) && $viewControllerInstance instanceof Closure) {
        #self::$viewGlobalContext = $viewControllerInstance;
        return call_user_func_array (self::lambda ($viewControllerInstance), self::defaultHandlerArguments ());
      } elseif (is_object ($viewControllerInstance) && method_exists ($viewControllerInstance, 'handler')) {
        self::$viewGlobalContext = $viewControllerInstance;
        call_user_func_array ([$viewControllerInstance, 'handler'], self::defaultHandlerArguments ());
      }
    }
  }

  protected static function beforeRenderOrAPIHandler () {
    $viewPath = dirname (self::GetViewPath ());
    
    # Run middlewares
    # $middlewaresList = [];

    $viewPathSlices = preg_split ('/(\/|\\\)+/', $viewPath);
    $viewPathSlicesCount = count ($viewPathSlices);

    for ($i = 0; $i < $viewPathSlicesCount; $i++) {
      $viewMiddlewarePath = join (DIRECTORY_SEPARATOR, [
        $viewPath,
        pathinfo ($viewPath, PATHINFO_FILENAME) . '.middleware.php'
      ]);

      if (is_null (self::$viewLayout)) {
        $viewLayoutPath = join (DIRECTORY_SEPARATOR, [
          $viewPath,
          pathinfo ($viewPath, PATHINFO_FILENAME) . '.layout.php'
        ]);

        if (is_file ($viewLayoutPath)) {
          self::$viewLayout = $viewLayoutPath;
        }
      }

      if (is_file ($viewMiddlewarePath)) {
        $viewMiddlewareInstance = require ($viewMiddlewarePath);

        if (is_callable ($viewMiddlewareInstance) && $viewMiddlewareInstance instanceof Closure) {
          call_user_func_array (self::lambda ($viewMiddlewareInstance), self::defaultHandlerArguments ());
        } elseif (is_object ($viewMiddlewareInstance) && method_exists ($viewMiddlewareInstance, 'handler')) {
          call_user_func_array ([$viewMiddlewareInstance, 'handler'], self::defaultHandlerArguments ());
        }
      }

      $viewPath = dirname ($viewPath);
    }
    # End
  }

  protected static function isAPIRoutePath ($routePath) {
    return preg_match ('/\/?api\/?/', $routePath);
  }

  /**
   * get the main layout path
   */
  protected static function mainLayoutView () {
    if (is_string (self::$viewLayout)
      && is_file (self::$viewLayout)) {
      return self::$viewLayout;
    }
    
    return dirname (__DIR__) . '/layouts/app.php';
  }

  /**
   * set the view path
   */
  protected static function setViewPath ($viewPath) {
    self::$viewPath = $viewPath;
  }

  /**
   * verify if a given file path exists in the public directory
   * 
   * @param string $publicFilePath
   */
  protected static function publicFileExists ($publicFilePath) {
    $publicPath = dirname (__DIR__) . '/public';

    $publicFilePath = join (DIRECTORY_SEPARATOR, [
      $publicPath, $publicFilePath
    ]);

    if (is_file ($publicFilePath)) {
      return realpath ($publicFilePath);
    }

    return false;
  }

  /**
   * Serve a static file given its absolute path
   * 
   * @param string $publicFilePath
   */
  protected static function serveStaticFile ($publicFilePath) {
    $fileExtension = pathinfo (strtolower ($publicFilePath), PATHINFO_EXTENSION);

    $mimetype = 'application/octet-stream';
    $mimetypeMap = mimetypemap ();
    
    if (isset ($mimetypeMap [$fileExtension])) {
      $mimetype = $mimetypeMap [$fileExtension];
    }

    @header ('X-Powered-By: Samils SY');
    @header ("Content-Type: {$mimetype}");

    exit (file_get_contents ($publicFilePath));
  }

  /**
   * Rewrite a route path to a regular expression
   */
  protected static function path2regex ($path) {
    $specialCharsList = '/[\/\^\$\[\]\{\}\(\)\\\\.]/';

    return preg_replace_callback (
      $specialCharsList, function ($match) {
        return '\\' . $match[0];
    }, (string)$path);
  }

  /**
   * 
   */
  protected static function defaultHandlerArguments () {
    if (!is_array (self::$defaultHandlerArguments)) {
      self::$defaultHandlerArguments = [
        Request::createFromGlobals (),
        new Utils\Http\Response
      ];
    }
    
    return self::$defaultHandlerArguments;
  }

  public static function lambda ($callback) {
    if (!($callback instanceof Closure)) {
      return;
    }

    if (!(is_object (self::$viewGlobalContext) )) {
      self::$viewGlobalContext = new BaseController;
    }

    return $callback->bindTo (self::$viewGlobalContext, get_class (self::$viewGlobalContext));
  }
  
  /**
   * get the view path
   */
  public static function GetViewPath () {
    return self::$viewPath;
  }

  /**
   * set the router path  prefix
   */
  public static function PathPrefix ($pathPrefix = null) {
    if (is_string ($pathPrefix) && !empty ($pathPrefix)) {
      $pathPrefix = preg_replace ('/^\/+/', '',
        preg_replace ('/\/+$/', '', preg_replace ('/\/{2,}/', '/', $pathPrefix))  
      );

      self::$pathPrefix ['pattern'] = '/^('.self::path2regex ($pathPrefix).')/i';
      self::$pathPrefix ['text'] = trim ($pathPrefix);
    }

    if (isset (self::$pathPrefix ['text'])) {
      return trim (self::$pathPrefix ['text']);
    }
  }
}
