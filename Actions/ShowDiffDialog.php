<?php
namespace axenox\Replay\Actions;

use exface\Core\Actions\ShowDialog;
use exface\Core\Widgets\Dialog;
use exface\Core\Widgets\AbstractWidget;
use exface\Core\Factories\WidgetFactory;
use exface\Core\CommonLogic\Constants\Icons;
use exface\Core\Interfaces\Tasks\TaskInterface;
use exface\Core\Interfaces\DataSources\DataTransactionInterface;
use exface\Core\Interfaces\Tasks\ResultInterface;
use exface\Core\Factories\DataSheetFactory;

/**
 * This action shows a dialog comparing the current test result to the reference one
 *
 * @author Andrej Kabachnik
 *        
 */
class ShowDiffDialog extends ShowDialog
{

    private $diff_widget_type = 'DiffText';

    protected function init()
    {
        $this->setIcon(Icons::COMPARE);
        $this->setInputRowsMin(1);
        $this->setInputRowsMax(1);
        $this->setPrefillWithFilterContext(false);
    }

    protected function perform(TaskInterface $task, DataTransactionInterface $transaction) : ResultInterface
    {
        // Fetch the currently saved test data
        $saved_test_data = DataSheetFactory::createFromObjectIdOrAlias($this->getWorkbench(), 'EXFACE.ACTIONTEST.TEST_STEP');
        $saved_test_data->getFilters()->addConditionFromString($saved_test_data->getMetaObject()->getUidAttributeAlias(), $this->getInputDataSheet($task)->getUidColumn()->getValues()[0], EXF_COMPARATOR_IN);
        $saved_test_data->getColumns()->addFromExpression('MESSAGE_CORRECT');
        $saved_test_data->getColumns()->addFromExpression('MESSAGE_CURRENT');
        $saved_test_data->getColumns()->addFromExpression('OUTPUT_CORRECT');
        $saved_test_data->getColumns()->addFromExpression('OUTPUT_CURRENT');
        $saved_test_data->getColumns()->addFromExpression('RESULT_CORRECT');
        $saved_test_data->getColumns()->addFromExpression('RESULT_CURRENT');
        $saved_test_data->getColumns()->addFromExpression('ACTION_DATA');
        $saved_test_data->getColumns()->addFromExpression('ACTION_ALIAS');
        $saved_test_data->dataRead();
        
        $this->getDialogWidget()->prefill($saved_test_data);
        
        return parent::perform($task, $transaction);
    }

    protected function enhanceDialogWidget(Dialog $dialog)
    {
        $dialog = parent::enhanceDialogWidget($dialog);
        
        // Create tabs for different things to compare
        $tabs = WidgetFactory::create($this->getWidgetDefinedIn()->getPage(), 'Tabs', $dialog);
        $tabs->addTab($this->createDiffWidget($dialog, 'OUTPUT_CORRECT', 'OUTPUT_CURRENT', 'Output'));
        $tabs->addTab($this->createDiffWidget($dialog, 'RESULT_CORRECT', 'RESULT_CURRENT', 'Result'));
        $tabs->addTab($this->createDiffWidget($dialog, 'MESSAGE_CORRECT', 'MESSAGE_CURRENT', 'Message'));
        $tabs->addTab($this->createDiffWidget($dialog, 'ACTION_DATA', 'ACTION_DATA', 'Action data'));
        $dialog->addWidget($tabs);
        
        // Add the accept button
        /* @var $button \exface\Core\Widgets\DialogButton */
        $button = $dialog->createButton();
        $button->setCaption('Accept changes');
        $button->setActionAlias('axenox.Replay.AcceptChanges');
        $button->setCloseDialogAfterActionSucceeds(true);
        $dialog->addButton($button);
        
        return $dialog;
    }

    public function getDiffWidgetType()
    {
        return $this->diff_widget_type;
    }

    public function setDiffWidgetType($value)
    {
        $this->diff_widget_type = $value;
        return $this;
    }

    protected function createDiffWidget(AbstractWidget $parent, $left_attribute_alias, $rigt_attribute_alias, $caption)
    {
        /* @var $widget \exface\Core\Widgets\DiffText */
        $widget = WidgetFactory::create($this->getWidgetDefinedIn()->getPage(), $this->getDiffWidgetType(), $parent);
        $widget->setLeftAttributeAlias($left_attribute_alias);
        $widget->setRightAttributeAlias($rigt_attribute_alias);
        $widget->setCaption($caption);
        return $widget;
    }
}
?>