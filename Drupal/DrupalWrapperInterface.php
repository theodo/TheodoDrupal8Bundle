<?php

namespace Theodo\Bundle\Drupal8Bundle\Drupal;

/**
 * Drupal interface connects a Drupal8 app with Symfony2 one.
 *
 * @author Thierry Marianne <thierrym@theodo.fr>
 * @author Kenny Durand <kennyd@theodo.fr>
 * @author Benjamin Grandfond <benjaming@theodo.fr>
 */
interface DrupalWrapperInterface
{

    /**
     *
     * @abstract
     * @param $request
     * @return Response
     */
    public function handle($request);

    /**
     *
     * @abstract
     * @return Drupal\Core\DrupalKernel
     */
    public function getDrupalKernel();

}
