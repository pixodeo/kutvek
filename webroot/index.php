<?php
use \Core\Routing\Router;
error_reporting(E_ERROR);
ini_set('display_errors', 1);
define('DS', DIRECTORY_SEPARATOR);
define('URL_SITE', 'https://demo.kutvek-kitgraphik.com/');
define('WEBROOT', dirname(__FILE__));
define('ROOT', dirname(WEBROOT));
define('CORE', ROOT.DS.'Core');
define('VENDOR', ROOT.DS.'Vendor');
define('COMPONENT', ROOT.DS.'Component');
define('_DOMAIN_', ROOT.DS.'Domain');
define('MIDDLEWARE', ROOT.DS.'Middleware');
define('PSR', ROOT.DS.'Psr');
define('STRIPE', ROOT.DS.'Stripe');
define('APP', ROOT.DS.'App');
define('LIBRARY', ROOT.DS.'Library');
define('APP_DIR', '\\App\\');
define('CONFIG', ROOT.DS.'Config');
define('CSS', WEBROOT.DS.'css');
define('IMG', WEBROOT.DS.'img');
define('IMG_SNOW', WEBROOT.DS.'webroot'.DS.'img');
define('IMAGES', WEBROOT.DS.'images');
define('IMAGES_PRODUITS', IMAGES . DS . 'produits' . DS . 'original');
define('IMG_SLIDER', IMG . DS . 'slider');
define('IMG_DIR', WEBROOT.DS.'pictures');
define('PRODUCT_IMG', IMG.DS.'products');
define('SEATCOVER_DIR', IMG.DS.'vehicles'.DS.'seat-covers');
define('JS', WEBROOT.DS.'js');
define('CACHE', WEBROOT.DS.'cache');
define('FILES_DIR', WEBROOT.DS.'files');
define('XML_DIR', WEBROOT.DS.'files'.DS.'xml');
define('MOCKUPS_DIR', WEBROOT.DS.'files'.DS.'mockups');
define('ORDERS_DIR', WEBROOT.DS.'orders');
define('USERS_DIR', WEBROOT.DS.'files'.DS.'users');
define('CORRESPONDENCES_DIR', WEBROOT.DS.'files'.DS.'correspondences');
define('VIEW_PATH', ROOT.DS.'View'. DS);
define('LAYOUT_PATH', VIEW_PATH . 'Layout' . DS);
define('CONTROLLER_DIR', '\\App\\Controller\\');
define('MIDDLEWARE_DIR', '\\Middleware\\');
define('MODEL_DIR', '\\App\\Model\\');
define('SERVER_NAME' , $_SERVER['SERVER_NAME']);
define('DOMAIN', 'https://demo.kutvek-kitgraphik.com');
define('FQDN', 'https://demo.kutvek-kitgraphik.com');
define('URL_API', 'https://dev.kutvek.com');
define('URL_FILES_ORDERS', FQDN . '/orders/');
define('URL_FILES_USERS', 'https://dev.kutvek.com/files/users/');
define('PRODUCT_IMG_URL', URL_SITE . 'img/products/');
define('PRODUITS_IMAGES_URL', URL_SITE . 'images/produits/original/');
define('SLIDER_IMG_URL', URL_SITE . 'img/slider/');
define('SEATCOVER_IMG_URL', URL_SITE . 'img/vehicles/seat-covers/');
define('HTTP_HOST', $_SERVER['HTTP_HOST']);
define('WORKSPACE', 2);
define('WORKPLACE', 2);
define('WEBSITE_ID', 5);
define('HALLOWEEN', 0);
require APP.DS.'App.php';
require CORE.DS.'Http'.DS.'Response'.DS.'send.php';
App::load();
$psr17Factory = new \Core\Http\Message\Factory\Psr17Factory();
$creator = new \Core\Http\Message\ServerRequestCreator(
    $psr17Factory, // ServerRequestFactory
    $psr17Factory, // UriFactory
    $psr17Factory, // UploadedFileFactory
    $psr17Factory  // StreamFactory
);
// Etape 1 objet request
$request = $creator->fromGlobals();
//$url = isset($_GET['url']) ? $_GET['url'] : 'pages.index';
$router = new Router($request);

$router->get('/', "page.homepage")
    ->middleware('PoweredBy');

$router->get('/asset/:file', 'assets')
    ->with('file', '([a-z0-9\-\/.]+)');   

// Cart
$router->put('/carts/:id/add-voucher', 'cart.addVoucher')
->with('id', '([0-9]+)');
$router->put('/carts/:id/delete-voucher', 'cart.deleteVoucher')
->with('id', '([0-9]+)');

$router->put('/carts/:id/items/:item/qty', 'cart.updateItemQty')
->with('id', '([0-9]+)')
->with('item', '([0-9]+)');

$router->delete('/carts/:id/items/:item', 'cart.deleteItem')
->with('id', '([0-9]+)')
->with('item', '([0-9]+)');

// Checkout
$router->get('/checkout/cart', 'checkout.cart')
    ->middleware('PoweredBy');

$router->get('/checkout/cart-overview', 'cart.overview')
    ->middleware('PoweredBy');  

$router->get('/checkout/shipping', 'checkout.shipping')
    ->middleware('PoweredBy');  

$router->get('/checkout/pay', 'checkout.payment')
    ->middleware('PoweredBy');

$router->get('/checkout/:id/add-shipping-address', 'checkout.addShippingAddress')
    ->with('id', '([0-9]+)');
$router->post('/checkout/:id/add-shipping-address', 'checkout.addShippingAddress')
    ->with('id', '([0-9]+)');

$router->post('/checkout/:order/:psp', 'checkout.create')    
    ->with('order', '([0-9]+)')   
    ->with('psp', '([a-z\-]+)')
    ->middleware('PoweredBy');

$router->post('/checkout/:order/:psp/capture', 'checkout.capture')    
    ->with('order', '([0-9]+)')   
    ->with('psp', '([a-z\-]+)')
    ->middleware('PoweredBy');

$router->get('/checkout/next', 'checkout.next')
    ->middleware('PoweredBy');

$router->get('/cart/stores', 'checkout.mapboxPoints')
    ->middleware('PoweredBy');    

$router->get('vehicle/:id/years', 'vehicle.graphicYears') 
    ->with('id', '([0-9]+)')
    ->middleware('PoweredBy'); 

$router->get('vehicle-years/:id/kit-types', 'vehicle.yearKitTypes') 
    ->with('id', '([0-9]+)')
    ->middleware('PoweredBy'); 

// Demander les gabarits / millesims d'un véhicule
$router->get('vehicles/:id/years/:year_id/year-types', 'vehicle.yearTypes') 
    ->with('id', '([0-9]+)')
    ->with('year_id', '([0-9]+)') 
    ->middleware('PoweredBy'); 

$router->get('/sportswear/:slug-:id', 'sportswear.read')    
    ->with('slug', '([a-z0-9\-\.]+)')    
    ->with('id', '([0-9]+)')
    ->middleware('PoweredBy');

$router->get('/products/:id/gallery', 'product.gallery')
    ->with('id', '([0-9]+)')
    ->middleware('PoweredBy');

$router->get('/products/:id/options', 'product.options')
    ->with('id', '([0-9]+)')
    ->middleware('PoweredBy');

$router->post('/sportswear', 'sportswear.addToCart')
    ->middleware('PoweredBy');

$router->get('/:slug/export', 'saddleCover.export')
    ->with('slug', '(housses-de-selle|seat-covers|coprisedili)')
    ->middleware('PoweredBy');

$router->get('/:slug', 'saddleCover.index')
    ->with('slug', '(housses-de-selle|seat-covers)')
    ->middleware('PoweredBy');

$router->get('/:section/:slug-:id', 'saddleCover.read')
    ->with('section', '(housses-de-selle|seat-covers|coprisedili)')
    ->with('slug', '([a-z0-9\-\.]+)')    
    ->with('id', '([0-9]+)')
    ->middleware('PoweredBy');

$router->post('/:slug', 'saddleCover.addToCart')
    ->with('slug', '(housses-de-selle|seat-covers|coprisedili)')
    ->middleware('PoweredBy');

$router->get('/:slug/filters', 'saddleCover.filter')
    ->with('slug', '(housses-de-selle|seat-covers|coprisedili)')
    ->middleware('PoweredBy');

$router->get('/:slug/refresh-filters', 'saddleCover.refreshFilter')
    ->with('slug', '(housses-de-selle|seat-covers|coprisedili)')
    ->middleware('PoweredBy');

$router->get('/:slug-:id', 'product.read')
    ->with('slug', '([a-z0-9\-\/\.]+)')
    ->with('id', '([0-9]+)')    
    ->middleware('PoweredBy')
    ->middleware('IsPage')
    ->middleware('Dispatch');

// Accès aux pages de contenu
$router->get('/:slug', 'page.read')
    ->with('slug', '([a-z0-9\-\/\~\.]+)')   
    ->middleware('PoweredBy')
    ->middleware('UrlStatus')
    ->middleware('Dispatch');

$router->post('/product/add-to-cart', 'product.addToCart')
    ->middleware('PoweredBy');   
     
try {
    $router->run();
} catch(\Exception $e) {
     
}