<?php

namespace App\Services\Ai;

/** Collected state of one agent run. */
class AgentState
{
    public string $text = '';
    public array $tools = [];
    public ?array $proposal = null;
    public ?string $proposalTool = null;

    public function addText(string $text): void
    {
        if (trim($text) === '') {
            return;
        }
        $this->text .= ($this->text === '' ? '' : "\n\n") . trim($text);
    }

    public function addTool(string $name, array $args, array $result): void
    {
        $this->tools[] = ['name' => $name, 'args' => $args, 'result' => $result];
    }

    public function isEmpty(): bool
    {
        return $this->text === '' && empty($this->tools);
    }
}
