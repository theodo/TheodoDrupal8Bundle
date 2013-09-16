<?php

namespace Theodo\Bundle\Drupal8Bundle\Drupal;

/**
 * Drupal interface connects a Drupal8 app with Symfony2 one.
 *
 * @author Thierry Marianne <thierrym@theodo.fr>
 * @author Kenny Durand <kennyd@theodo.fr>
 * @author Benjamin Grandfond <benjaming@theodo.fr>
 */
interface DrupalInterface
{
    /**
     * Initialize the Drupal instance
     *
     * @abstract
     *
     * @return mixed
     */
    public function initialize();

    /**
     * The shutdown method only catch exit instruction from the Drupal code to rebuild the correct response
     *
     * @abstract
     * @return mixed
     */
    public function shutdown();

    /**
     * Return true if the current Drupal object contains a valid Response object
     *
     * @abstract
     * @return bool
     */
    public function hasResponse();

    /**
     * Return the response returned by Drupal.
     *
     * @abstract
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getResponse();
}
