<?php
/**
 * @copyright   2014 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\ReportBundle\Generator;

use Doctrine\DBAL\Connection;
use Mautic\ReportBundle\Entity\Report;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;

/**
 * Report generator.
 */
class ReportGenerator
{
    /**
     * @var Connection
     */
    private $db;

    /**
     * @var EventDispatcher
     */
    private $dispatcher;

    /**
     * @var \Symfony\Component\Form\FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var \Mautic\ReportBundle\Entity\Report
     */
    private $entity;

    /**
     * @var string
     */
    private $validInterface = 'Mautic\\ReportBundle\\Builder\\ReportBuilderInterface';

    /**
     * @var string
     */
    private $contentTemplate;

    /**
     * ReportGenerator constructor.
     *
     * @param EventDispatcherInterface $dispatcher
     * @param Connection               $db
     * @param FormFactoryInterface     $formFactory
     * @param Report                   $entity
     * @param array                    $options
     */
    public function __construct(EventDispatcherInterface $dispatcher, Connection $db, Report $entity, FormFactoryInterface $formFactory = null)
    {
        $this->db          = $db;
        $this->dispatcher  = $dispatcher;
        $this->formFactory = $formFactory;
        $this->entity      = $entity;
    }

    /**
     * Gets query.
     *
     * @param array $options Optional options array for the query
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    public function getQuery(array $options = [])
    {
        $builder = $this->getBuilder();

        $query = $builder->getQuery($options);

        $this->contentTemplate = $builder->getContentTemplate();

        return $query;
    }

    /**
     * Gets form.
     *
     * @param \Mautic\ReportBundle\Entity\Report $entity  Report Entity
     * @param array                              $options Parameters set by the caller
     *
     * @return \Symfony\Component\Form\Form
     */
    public function getForm(Report $entity, $options)
    {
        return $this->formFactory->createBuilder('report', $entity, $options)->getForm();
    }

    /**
     * Gets the getContentTemplate path.
     *
     * @return string
     */
    public function getContentTemplate()
    {
        return $this->contentTemplate;
    }

    /**
     * Gets report builder.
     *
     * @return \Mautic\ReportBundle\Builder\ReportBuilderInterface
     *
     * @throws \Symfony\Component\DependencyInjection\Exception\RuntimeException
     */
    protected function getBuilder()
    {
        $className = '\\Mautic\\ReportBundle\\Builder\\MauticReportBuilder';

        if (!class_exists($className)) {
            throw new RuntimeException('The MauticReportBuilder does not exist.');
        }

        $reflection = new \ReflectionClass($className);

        if (!$reflection->implementsInterface($this->validInterface)) {
            throw new RuntimeException(
                sprintf("ReportBuilders have to implement %s, and %s doesn't implement it", $this->validInterface, $className)
            );
        }

        return $reflection->newInstanceArgs([$this->dispatcher, $this->db, $this->entity]);
    }
}
