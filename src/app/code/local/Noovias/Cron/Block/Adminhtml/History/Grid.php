<?php
/**
 * Noovias_Cron_Block_Adminhtml_History_Grid
 *
 * NOTICE OF LICENSE
 *
 * Noovias_Cron is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Noovias_Cron is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Noovias_Cron. If not, see <http://www.gnu.org/licenses/>.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Noovias_Cron to newer
 * versions in the future. If you wish to customize Noovias_Cron for your
 * needs please refer to http://www.noovias.com for more information.
 *
 * @category    Noovias
 * @package        Noovias_Cron
 * @copyright   Copyright (c) 2010 <info@noovias.com> - noovias.com
 * @license     <http://www.gnu.org/licenses/>
 *                 GNU General Public License (GPL 3)
 * @link        http://www.noovias.com
 */

/**
 * @category       Noovias
 * @package        Noovias_Cron
 * @copyright      Copyright (c) 2011 <info@noovias.com> - noovias.com
 * @license        http://opensource.org/licenses/osl-3.0.php
 *                 Open Software License (OSL 3.0)
 * @author      noovias.com - Core Team <info@noovias.com>
 */
class Noovias_Cron_Block_Adminhtml_History_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * @var $factory Noovias_Cron_Model_Factory
     */
    protected $factory = null;
    /**
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('cron_history_grid');
        $this->setUseAjax(true);
        $this->setDefaultSort('scheduled_at');
        $this->setDefaultDir('ASC');
        $this->setRowClickCallback();
        $this->setSaveParametersInSession(true);
    }

    /**
     * @return this
     */
    protected function _prepareCollection()
    {
        /**
         * @var Mage_Cron_Model_Mysql4_Schedule_Collection $collection
         */
        $collection = $this->getFactory()->getModelCronSchedule()->getCollection();

        // Filter Finished Schedules
        $success = Mage_Cron_Model_Schedule::STATUS_SUCCESS;
        $error = Mage_Cron_Model_Schedule::STATUS_ERROR;
        $missed = Mage_Cron_Model_Schedule::STATUS_MISSED;
        $collection->addFieldToFilter('status', array($success, $error, $missed));

        // Order Collection
        $collection->addOrder($this->getOrderSort(), $this->getOrderDir());

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('schedule_id', array(
            'header' => $this->helperCron()->__('Id'),
            'width' => '5px',
            'type' => 'text',
            'index' => 'schedule_id',
            'filter' => false,
        ));

        $this->addColumn('job_code', array(
            'header' => $this->helperCron()->__('Code'),
            'width' => '200px',
            'type' => 'options',
            'index' => 'job_code',
            'options' => Mage::getSingleton('noovias_cron/system_config_code')->get(),
        ));

        $this->addColumn('status', array(
            'header' => $this->helperCron()->__('Status'),
            'width' => '5px',
            'type' => 'options',
            'index' => 'status',
            'options' => Mage::getSingleton('noovias_cron/system_config_status')->getStatesHistory(),
        ));

        $this->addColumn('starting_in', array(
            'header' => $this->helperCron()->__('Starting In'),
            'index' => 'starting_in',
            'sortable' => false,
            'filter' => false,
            'type' => 'text',
            'width' => '80px',
            'renderer' => 'noovias_cron/adminhtml_widget_grid_column_renderer_starting',
        ));

        $this->addColumn('scheduled_at', array(
            'header' => $this->helperCron()->__('Scheduled'),
            'index' => 'scheduled_at',
            'type' => 'datetime',
            'width' => '100px',
            'renderer' => 'noovias_extensions/adminhtml_widget_grid_column_renderer_datetime',
        ));

        $this->addColumn('executed_at', array(
            'header' => $this->helperCron()->__('Executed'),
            'index' => 'executed_at',
            'type' => 'text',
            'width' => '100px',
            'renderer' => 'noovias_extensions/adminhtml_widget_grid_column_renderer_datetime',
        ));

        $this->addColumn('finished_at', array(
            'header' => $this->helperCron()->__('Finished'),
            'index' => 'finished_at',
            'type' => 'text',
            'width' => '100px',
            'renderer' => 'noovias_extensions/adminhtml_widget_grid_column_renderer_datetime',
        ));

        $this->addColumn('runtime', array(
            'header' => $this->helperCron()->__('Runtime'),
            'index' => 'runtime',
            'sortable' => false,
            'filter' => false,
            'type' => 'text',
            'width' => '60px',
            'renderer' => 'noovias_cron/adminhtml_widget_grid_column_renderer_runtime',
        ));

        $this->addColumn('created_at', array(
            'header' => $this->helperCron()->__('Created'),
            'index' => 'created_at',
            'type' => 'datetime',
            'width' => '80px',
            'renderer' => 'noovias_extensions/adminhtml_widget_grid_column_renderer_datetime',
        ));

        $this->addColumn('messages', array(
            'header' => $this->helperCron()->__('Messages'),
            'width' => '5px',
            'type' => 'text',
            'index' => 'messages',
        ));

        return parent::_prepareColumns();
    }

    /**
     * @return Noovias_Cron_Block_Adminhtml_History_Grid
     */
    protected function _prepareMassaction()
    {
        // No massDeleteAction required, Crons are periodically removed anyway.

        return $this;
    }

    /**
     * @return mixed
     */
    protected function getOrderSort()
    {
        return $this->getRequest()->getParam($this->getVarNameSort(), $this->_defaultSort);
    }

    /**
     * @return mixed
     */
    protected function getOrderDir()
    {
        return $this->getRequest()->getParam($this->getVarNameDir(), $this->_defaultDir);
    }

    /**
     * Url for clicking a row
     * (empty, because the history grid does not allow editing)
     * @param $row
     * @return bool
     */
    public function getRowUrl($row)
    {
        return false;
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }

    /**
     *
     * @return Noovias_Cron_Helper_Data
     */
    protected function helperCron()
    {
        return Mage::helper('noovias_cron');
    }

    /**
     * @param Noovias_Cron_Model_Factory $factory
     */
    public function setFactory(Noovias_Cron_Model_Factory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @return Noovias_Cron_Model_Factory
     */
    protected function getFactory()
    {
        if($this->factory === null){
            $this->factory = $this->helperCron()->getFactory();
        }
        return $this->factory;
    }

}
