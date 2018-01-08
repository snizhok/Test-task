<?php

class Parser
{
    const PATTERN = '/^([\+\-\*\/\(\)]|\d+)/';

    protected $output = [];

    protected $stack = [];

    protected $evaluationStack = [];

    protected $stackPriority = [
        '(' => 0,
        ')' => 0,
        '+' => 1,
        '-' => 1,
        '*' => 2,
        '/' => 2,
    ];

    private $rules;

    private $prevValue = '';

    public function generateString($rules, $start)
    {
        $this->rules = $rules;

        if (!isset($this->rules[$start])) {
            throw new Exception('Start does not exist');
        }
        $key = array_rand($this->rules[$start]);
        $part = $this->rules[$start][$key];

        $re = '/((<[a-z]+>))/';
        $result = preg_replace_callback($re, function ($match) use ($rules) {
            return $this->generateString($rules, $match[1]);
        }, $part);
        return $result;
    }

    public function evaluate($input)
    {
        $input = preg_replace('/\s+/', '', $input);

        while (strlen($input)) {
            preg_match(self::PATTERN, $input, $match);
            $input = substr($input, strlen($match[1]));
            $value = $match[1];

            if (!is_numeric($this->prevValue) && !is_numeric($value)
                && $value != '(' && $this->prevValue != ')') {
                if (in_array($value, ['*', '/'])) {
                    $this->output[] = '1';
                } else {
                    $this->output[] = '0';
                }
            }
            $this->prevValue = $value;

            if (is_numeric($value)) {
                $this->output[] = $value;
                continue;
            }
            $valPriority = $this->stackPriority[$value];
            switch ($value) {
                case '+':
                case '-':
                case '*':
                case '/':
                    $stackVal = end($this->stack);
                    $stackValPriority = $this->stackPriority[$stackVal];
                    while ($valPriority <= $stackValPriority) {
                        if ($stackVal == '(') {
                            break;
                        }
                        $stackVal = array_pop($this->stack);
                        $this->output[] = $stackVal;
                        $stackVal = end($this->stack);
                        $stackValPriority = $this->stackPriority[$stackVal];
                    }
                    $this->stack[] = $value;
                    break;
                case '(':
                    $this->stack[] = $value;
                    break;
                case ')':
                    $stackVal = array_pop($this->stack);
                    do {
                        $this->output[] = $stackVal;
                        $stackVal = array_pop($this->stack);
                    } while ($stackVal !== '(');
                    break;
            }
        }
        while (count($this->stack)) {
            $stackVal = array_pop($this->stack);
            $this->output[] = $stackVal;
        }

        foreach ($this->output as $value) {
            if (is_numeric($value)) {
                $this->evaluationStack[] = $value;
                continue;
            }
            $val1 = array_pop($this->evaluationStack);
            $val2 = array_pop($this->evaluationStack);
            $result = 0;
            switch ($value) {
                case '+':
                    $result = $val2 + $val1;
                    break;
                case '-':
                    $result = $val2 - $val1;
                    break;
                case '/':
                    $result = $val2 / $val1;
                    break;
                case '*':
                    $result = $val2 * $val1;
                    break;
            }
            $this->evaluationStack[] = $result;
        }
        return array_shift($this->evaluationStack);
    }
}

$rules = [
    '<expr>'   => ['<term>', '<term><add><term>'],
    '<term>'   => ['<factor>', '<factor><mult><factor>', '<factor><mult><term>'],
    '<factor>' => ['<neg>', '-<neg>'],
    '<neg>'    => ['<number>', '(<expr>)'],
    '<add>'    => ['+', '-'],
    '<mult>'   => ['*', '/'],
    '<number>' => ['<digit>', '<number><digit>'],
    '<digit>'  => [0, 1, 2, 3, 4, 5, 6, 7, 8, 9],
];

$parser = new Parser();
// Generate an expression by BNF
$expression = $parser->generateString($rules, '<expr>');
// Evaluate an expression
$value = $parser->evaluate('200+12*((1/8)+1)-19');
