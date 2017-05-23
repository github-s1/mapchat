<?php
class PushoverException extends Exception
{
    /**
     * Messages array
     * @var array
     */
    private $fMessages;

    /**
     * Exception constructor
     * @param array $aMessages An array of messages
     */
    public function __construct(array $aMessages)
    {
        parent::__construct('PushoverException exception');
        $this->fMessages = $aMessages;
    }

    /**
     * Get messages array
     * @return array
     */
    public function getMessages()
    {
        return empty($this->fMessages) ? array() : $this->fMessages;
    }
}