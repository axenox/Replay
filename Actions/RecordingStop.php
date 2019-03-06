<?php
namespace axenox\Replay\Actions;

use exface\Core\CommonLogic\Contexts\ContextActionTrait;
use exface\Core\CommonLogic\Constants\Icons;
use exface\Core\Factories\ResultFactory;
use exface\Core\Interfaces\Tasks\TaskInterface;
use exface\Core\Interfaces\DataSources\DataTransactionInterface;
use exface\Core\Interfaces\Tasks\ResultInterface;

/**
 * This action switches on the record mode in the ActionTest context
 *
 * @author Andrej Kabachnik
 *        
 */
class RecordingStop extends RecordingStart
{
    use ContextActionTrait;

    /**
     * 
     * {@inheritDoc}
     * @see \axenox\Replay\Actions\RecordingStart::init()
     */
    protected function init()
    {
        parent::init();
        $this->setIcon(Icons::STOP);
    }

    /**
     * 
     * {@inheritDoc}
     * @see \axenox\Replay\Actions\RecordingStart::perform()
     */
    protected function perform(TaskInterface $task, DataTransactionInterface $transaction) : ResultInterface
    {
        if ($this->getContext()->isRecording()) {
            $this->getContext()->recordingStop();
            $message = $this->getApp()->getTranslator()->translate('ACTION.RECORDINGSTOP.STOPPED');
        } else {
            $message = $this->getApp()->getTranslator()->translate('ACTION.RECORDINGSTOP.NOT_RECORDING');
        }
        return ResultFactory::createMessageResult($task, $message);
    }
}
?>