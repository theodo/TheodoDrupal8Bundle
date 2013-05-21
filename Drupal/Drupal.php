<?php

namespace Theodo\Bundle\DrupalBundle\Drupal;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Drupal\Core\DrupalKernel;

/**
 * Drupal class
 *
 * @author Thierry Marianne <thierrym@theodo.fr>
 * @author Kenny Durand <kennyd@theodo.fr>
 * @author Benjamin Grandfond <benjaming@theodo.fr>
 */
class Drupal implements DrupalInterface
{
    /**
     * Indicates whether Drupal is initialized or not
     * @var bool
     */
    protected $initialized = false;

    /**
     * The root path of the Drupal core files
     * @var string
     */
    protected $root;

    /**
     * Indicates whether the Drupal core is encapsulated
     * @var bool
     */
    protected $encapsulated = false;

    /**
     * @var ContainerInterface|null
     */
    protected $container;

    /**
     * @var String
     */
    protected $environment;

    /**
     * {@inheritdoc}
     */
    protected $response;

    /**
     * @param $root
     * @param ContainerInterface $serviceContainer
     */
    public function __construct($root, ContainerInterface $serviceContainer)
    {
        $this->container = $serviceContainer;
        $this->root      = $root;
    }

    /**
     * Initialize the Drupal core
     */
    public function initialize($environment)
    {
        if ($this->initialized) {
            return;
        }

        $this->environment = $environment;

        $this->initialized = true;
        register_shutdown_function(array($this, 'shutdown'));

        $this->encapsulate(array($this, 'generateDrupalResponse'), array($this->root));
    }

    /**
     * State of initilize.
     *
     * @return boolean
     */
    public function isInitialized()
    {
        return $this->initialized;
    }

    /**
     * The shutdown method only catch exit instruction from Drupal
     * to rebuild the correct response
     *
     * @return mixed
     */
    public function shutdown()
    {
        if (!$this->encapsulated) {
            return;
        }

        if (null == $this->response) {
            $this->response = new Response();
        }

        $this->response->setContent(ob_get_contents());
        $this->response->send();
    }

    /**
     * Encapsulate the Drupal8 request handling call.
     */
    protected function encapsulate($function, $arguments)
    {
        $this->response = call_user_func_array($function, $arguments);

        $this->encapsulated = false;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Return true if the current drupal object contains a valid Response object
     *
     * @return bool
     */
    public function hasResponse()
    {
        return !empty($this->response);
    }

    /**
     * Bootstraps the Drupal Kernel and tries to get the Response object
     */
    public function generateDrupalResponse($path)
    {
        ob_start();
        $response = null;

        // make sure the default path points to Drupal root
        $currentDir = getcwd();
        chdir($path);

        require_once $path . '/core/includes/bootstrap.inc';

        // Initialize the environment, load settings.php, and activate a PSR-0 class
        // autoloader with required namespaces registered.
        drupal_bootstrap(DRUPAL_BOOTSTRAP_CONFIGURATION);

        // Exit if we should be in a test environment but aren't.
        if ($this->environment !== 'test' && !drupal_valid_test_ua()) {
            throw new \RuntimeException(sprintf('The environment should be test, %env% given', $this->environment));
        }

        // @todo Figure out how best to handle the Kernel constructor parameters.
        $kernel = new DrupalKernel('prod', false, drupal_classloader(), $this->environment !== 'test');

        // @todo Remove this once everything in the bootstrap has been
        //   converted to services in the DIC.
        $kernel->boot();
        drupal_bootstrap(DRUPAL_BOOTSTRAP_CODE);

        if ($this->container->isScopeActive('request')) {
            $request = $this->container->get('request');
            $response = $kernel->handle($request)->prepare($request);
            $kernel->terminate($request, $response);
        }

        // restore the symfony error handle
        restore_error_handler();
        restore_exception_handler();

        chdir($currentDir);

        ob_end_clean();

        return $response;
    }
}
