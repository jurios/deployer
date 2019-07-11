<?php


namespace Kodilab\Deployer\Changes;


class Rename extends Change
{
    /** @var string */
    protected $from;

    /** @var string */
    protected $to;

    public function __construct(string $from, string $to)
    {
        parent::__construct($to);

        $this->from = $from;
        $this->to = $to;
    }

    public function from()
    {
        return $this->from;
    }

    public function to()
    {
        return $this->to;
    }

    public function is(Change $change)
    {
        return parent::is($change)
            && $this->from() === $change->from()
            && $this->to() === $change->to();
    }
}