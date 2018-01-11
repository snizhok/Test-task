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
    protected $prevValue;

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
                    if (isset($this->stackPriority[$stackVal])) {
                        $stackValPriority = $this->stackPriority[$stackVal];
                        while ($valPriority <= $stackValPriority) {
                            if ($stackVal == '(') {
                                break;
                            }
                            $stackVal = array_pop($this->stack);
                            $this->output[] = $stackVal;
                            $stackVal = end($this->stack);
                            $stackValPriority = isset($this->stackPriority[$stackVal])
                                ? $this->stackPriority[$stackVal]
                                : 0;
                        }
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
$parser = new Parser();
// Evaluate an expression
var_dump($parser->evaluate('200+12*((1/8)+1)-19'));
