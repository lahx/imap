<?php

namespace Ddeboer\Imap;

/**
 * An IMAP mailbox (commonly referred to as a ‘folder’)
 *
 */
class Mailbox implements \IteratorAggregate
{
    protected $name;
    protected $stream;
    protected $messageIds;

    /**
     * Constructor
     *
     * @param string   $name   Mailbox name
     * @param resource $stream PHP IMAP resource
     */
    public function __construct($mailbox, $stream)
    {
        $this->mailbox = $mailbox;
        $this->stream = $stream;
        $this->name = substr($mailbox, strpos($mailbox, '}')+1);
    }

    /**
     * Get mailbox name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get number of messages in this mailbox
     *
     * @return int
     */
    public function count()
    {
        $this->init();
        return \imap_num_msg($this->stream);
    }

    /**
     * Get message ids
     *
     * @return array
     */
    public function getMessages()
    {
        if (null === $this->messageIds) {
            $this->init();
            $this->messageIds = \imap_search($this->stream, 'ALL');
        }

        return $this->messageIds;
    }

    /**
     * Get a message by message number
     *
     * @param int $number Message number
     *
     * @return Message
     */
    public function getMessage($number)
    {
        $this->init();

        return new Message($this->stream, $number);
    }

    /**
     * Get messages in this mailbox
     *
     * @return MessageIterator
     */
    public function getIterator()
    {
        $this->init();

        return new MessageIterator($this->stream);
    }

    protected function init()
    {
        $check = \imap_check($this->stream);
        if ($check->Mailbox != $this->mailbox) {
            \imap_reopen($this->stream, $this->mailbox);
        }
    }
}