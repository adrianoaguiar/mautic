<?php
/**
 * @copyright   2014 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
$view->extend('MauticCoreBundle:Default:content.html.php');
$view['slots']->set('mauticContent', 'campaign');
$view['slots']->set('headerTitle', $view['translator']->trans('mautic.campaign.campaigns'));

$view['slots']->set(
    'actions',
    $view->render(
        'MauticCoreBundle:Helper:page_actions.html.php',
        [
            'templateButtons' => [
                'new' => $permissions['campaign:campaigns:create'],
            ],
            'routeBase' => 'campaign',
        ]
    )
);
?>

<div class="panel panel-default bdr-t-wdh-0">
	<?php echo $view->render('MauticCoreBundle:Helper:list_toolbar.html.php', [
        'searchValue'     => $searchValue,
        'searchHelp'      => 'mautic.core.help.searchcommands',
        'action'          => $currentRoute,
        'routeBase'       => 'campaign',
        'templateButtons' => [
            'delete' => $permissions['campaign:campaigns:delete'],
        ],
        'filters' => $filters,
    ]); ?>

    <div class="page-list">
        <?php $view['slots']->output('_content'); ?>
    </div>
</div>