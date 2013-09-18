<?php

namespace Theodo\Bundle\Drupal8Bundle\Drupal;

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
 * @author Fabrice Bernhard <fabriceb@theodo.fr>
 */
class DrupalWrapper implements DrupalWrapperInterface
{

    /**
     * The root path of the Drupal core files
     * @var string
     */
    private $drupalDir;

    /**
     * The root path of the Drupal core files
     * @var string
     */
    private $drupalKernel;

    /**
     * Indicates whether the Drupal core has exited cleanly
     * @var bool
     */
    private $cleanDrupalExit = false;


    /**
     * {@inheritdoc}
     */
    private $response;

    /**
     * @param $drupalDir
     * @param ContainerInterface $serviceContainer
     */
    public function __construct($drupalDir)
    {
        $this->drupalDir = $drupalDir;
    }

    /**
     * The shutdown method only catch exit instruction from Drupal
     * to rebuild the correct response
     *
     * @return mixed
     */
    public function catchExit()
    {
        if (!$this->catchExit) {
            return;
        }

        if (null == $this->response) {
            $this->response = new Response();
        }

        $this->response->setContent(ob_get_contents());
        $this->response->send();
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

    private function bootDrupalKernel() {
        $currentDir = getcwd();
        chdir($this->drupalDir);

        require_once $this->drupalDir . '/core/includes/bootstrap.inc';

        // Initialize the environment, load settings.php, and activate a PSR-0 class
        // autoloader with required namespaces registered.
        drupal_bootstrap(DRUPAL_BOOTSTRAP_CONFIGURATION);

        $drupalKernel = new DrupalKernel('prod', drupal_classloader());
        $drupalKernel->boot();

        drupal_bootstrap(DRUPAL_BOOTSTRAP_CODE);

        chdir($currentDir);

        return $drupalKernel;
    }

    public function getDrupalKernel() {
        if (null === $this->drupalKernel) {
            $this->drupalKernel = $this->bootDrupalKernel();
        }

        return $this->drupalKernel;
    }

    /**
     * Bootstraps the Drupal Kernel and tries to get the Response object
     */
    public function handleRequest($request)
    {
        // handle possible exit() in Drupal code using a register shutdown function
        $this->catchExit = true;
        register_shutdown_function(array($this, 'catchExit'));

        ob_start();
        $response = null;

        // make sure the default path points to Drupal root
        $currentDir = getcwd();
        chdir($this->drupalDir);

        $drupalKernel = $this->getDrupalKernel();

        $response = $drupalKernel->handle($request);
        $response = $response->prepare($request);
        $drupalKernel->terminate($request, $response);

        // we can no exit() used in Drupal code
        $this->catchExit = false;

        // restore the symfony error handle
        restore_error_handler();
        restore_exception_handler();

        chdir($currentDir);

        ob_end_clean();

        return $response;
    }
}
