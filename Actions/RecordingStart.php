<?php
namespace axenox\Replay\Actions;

use exface\Core\CommonLogic\AbstractAction;
use exface\Core\CommonLogic\Contexts\ContextActionTrait;
use exface\Core\CommonLogic\Constants\Icons;
use exface\Core\Interfaces\Actions\iModifyContext;
use exface\Core\Interfaces\Contexts\ContextManagerInterface;
use exface\Core\Interfaces\Tasks\TaskInterface;
use exface\Core\Interfaces\DataSources\DataTransactionInterface;
use exface\Core\Interfaces\Tasks\ResultInterface;
use exface\Core\Factories\ResultFactory;

/**
 * This action switches on the record mode in the ActionTest context
 *
 * @author Andrej Kabachnik
 *        
 */
class RecordingStart extends AbstractAction implements iModifyContext
{
    use ContextActionTrait;

    /**
     * 
     * {@inheritDoc}
     * @see \exface\Core\CommonLogic\AbstractAction::init()
     */
    protected function init()
    {
        parent::init();
        $this->setIcon(Icons::CIRCLE);
        $this->setContextScope(ContextManagerInterface::CONTEXT_SCOPE_WINDOW);
        $this->setContextAlias('axenox.Replay.ActionTestContext');
    }

    /**
     * 
     * {@inheritDoc}
     * @see \exface\Core\CommonLogic\AbstractAction::perform()
     */
    protected function perform(TaskInterface $task, DataTransactionInterface $transaction) : ResultInterface
    {
        if ($this->getContext($task)->isRecording()) {
            $message = $this->getApp()->getTranslator()->translate('ACTION.RECORDINGSTART.ALREADY_RECORDING');
        } else {
            $this->getContext($task)->recordingStart();
            $message = $this->getApp()->getTranslator()->translate('ACTION.RECORDINGSTART.STARTED');
        }
        return ResultFactory::createMessageResult($task, $message);
    }
}
?>