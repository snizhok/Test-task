<?php

/**
 * Class BNFParser
 *
 * Parser for the expressions which can be defined by the BNF:
 * <expr> ::= <term> | <term> <add> <term>
 * <term> ::= <factor> | <factor> <mult> <factor> | <factor> <mult> <term>
 * <factor> ::= <neg> | - <neg>
 * <neg> ::= <number> | ( <expr> )
 * <add> ::= + | -
 * <mult> ::= * | /
 * <number> ::= <digit> | <number> <digit>
 * <digit> ::= 0 | 1 | 2 | 3 | 4 | 5 | 6 | 7 | 8 | 9
 *
 */
class BNFParser
{
    protected $digits = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
    protected $mults = ['*', '/'];
    protected $adds = ['+', '-'];

    protected $expression;
    protected $results = [];
    protected $error = null;


    public function evaluate(array $expressions)
    {
        foreach ($expressions as $expression) {
            $this->error = null;
            $this->expression = preg_replace('/\s+/', '', $expression);
            $original_expression = $this->expression;
           // $this->check();
            $result = $this->expr();
            $this->results[] = [
                'expression' => $original_expression,
                'result'     => $this->error ? $this->error : $result
            ];

        }
        return $this->results;
    }

    protected function check()
    {
        if (!preg_match('~^[\d\+\-\*\/\(\)]+$~', $this->expression)) {
            $this->error = 'Expression does not match the BNF';
            return;
        }

        $malformedPatterns = [
            '~(\+|\-|\*|\/){2,}|(\(\))+~',
            '~(\d\()|(\)\d)|(\)\()|([\+|\-|\*|\/]\))~'
        ];
        foreach ($malformedPatterns as $pattern) {
            if (preg_match($pattern, $this->expression)) {
                $this->error = 'Malformed expression';
                return;
            }
        }

        $pCount = 0;
        for ($i = 0; $i < strlen($this->expression); $i++) {
            if ($this->expression[$i] == '(') {
                $pCount++;
            }
            if ($this->expression[$i] == ')') {
                $pCount--;

            }
        }
        if ($pCount != 0) {
            $this->error = 'Malformed expression';
        }
        return;
    }

    protected function checkNextChar()
    {
        $nextChar = substr($this->expression, 1, 1);
        if (in_array($nextChar, $this->adds)
            || in_array($nextChar, $this->mults)
            || $nextChar == ')') {
            $this->error = 'Expression does not match the BNF';
            return false;
        }
        return true;
    }

    protected function expr()
    {
        if ($this->error) {
            return;
        }
        $result = $this->term();
        if (!strlen($this->expression)) {
            return $result;
        }
        $add = substr($this->expression, 0, 1);
        if (in_array($add, $this->adds) && strlen($this->expression) > 1) {
            if (!$this->checkNextChar()) {
                return;
            }
            $this->expression = substr($this->expression, 1);
            switch ($add) {
                case '+':
                    $result = $result + $this->term();
                    break;
                case '-':
                    $result = $result - $this->term();
                    break;
            }
            if (strlen($this->expression)) {
                $this->error = 'Expression does not match the BNF';
                return;
            }
            return $result;
        }
        $this->error = 'Expression does not match the BNF';
        return;
    }

    protected function term()
    {
        if ($this->error) {
            return;
        }
        $result = $this->factor();
        if (strlen($this->expression) < 2) {
            return $result;
        }
        $mult = substr($this->expression, 0, 1);
        while (in_array($mult, $this->mults)) {
            if (!$this->checkNextChar()) {
                return;
            }

            $this->expression = substr($this->expression, 1);
            $factor = $this->factor();
            switch ($mult) {
                case '*':
                    $result = $result * $factor;
                    break;
                case '/':
                    if ($factor == 0) {
                        $this->error = 'Division by zero';
                        return;
                    }
                    $result = $result / $factor;
                    break;
            }
            $mult = substr($this->expression, 0, 1);
        }
        return $result;

    }

    protected function factor()
    {
        if ($this->error) {
            return;
        }
        $minusSign = substr($this->expression, 0, 1) == '-';
        if ($minusSign) {
            if (!$this->checkNextChar()) {
                return;
            }
            $this->expression = substr($this->expression, 1);
        }
        return $minusSign ? -1 * $this->neg() : $this->neg();
    }

    protected function neg()
    {
        if ($this->error) {
            return;
        }
        $lp = substr($this->expression, 0, 1) == '(';
        if ($lp) {
            $pCount = 0;
            for ($i = 0; $i <= strlen($this->expression); $i++) {
                if (!isset($this->expression[$i])) {
                    $this->error = 'Expression does not match the BNF';
                    return;
                }
                if ($this->expression[$i] == '(') {
                    $pCount++;
                }
                if ($this->expression[$i] == ')') {
                    $pCount--;

                }
                if ($pCount == 0) {
                    break;
                }
            }
            $expression = substr($this->expression, 1, $i - 1);
            if (empty($expression)) {
                $this->error = 'Expression does not match the BNF';
                return;
            }
            $expressionEnd = substr($this->expression, $i + 1);

            $this->expression = $expression;
            $result = $this->expr();

            $this->expression = $expressionEnd;
            return $result;
        }
        return $this->number();
    }

    protected function number()
    {
        if ($this->error) {
            return;
        }
        if (strlen($this->expression)) {
            $digit = substr($this->expression, 0, 1);
            if (in_array($digit, $this->digits)) {
                $this->expression = substr($this->expression, 1);
                return $digit . $this->number();
            }
            return;
        }
        return;
    }
}

$parser = new BNFParser();

$expressions = [
    // Valid expression
    '2*(2+1)*3',
    '10+2*6',
    '100*2+12',
    '100*(2+12)',
    '5*(6+2)-12/4',
    '(100*(2+12))/14',
    '100*((2+12)/14)',
    '((22+43)/(24*98)+29)/7',
    '((((12+9*9/8))))',
    '(2+1)*9',
    '(2)+(3)',
    '-(6*9)/8+6',

    // ?
    '-200+12*((1/8)+1)-19',
    '1+2*3*(7/8)-(45-10)',

    // Expressions that cause errors
    '5*(6+2)-12/4.2',
    '(1+9*4)/0',

    // Malformed expressions
    '()',
    '5++2',
    '((22+43)/(24*98)+29)/7)',
    '((22+43)/(24*98)+29/7',
    '(22+43)/(24*98))+29/7',
    '1(2)3',
    '5**2',
    '5--3+2*89',
    '(2+2)6',
    '--2*9',
    '++2*9',
    '(2+1)**3',
    '(2+)+1',
    '(2/)+1',
    '(2)(3)',

    // Invalid Expressions
    '5*2-9^8',
    '((22+43)/(24*98)+29)/7a',
    '((22+43):(24*98)+29)',
];

$results = $parser->evaluate($expressions);
foreach ($results as $result) {
    echo $result['expression'] . ' -> ' . $result['result'] . "\n\n";
}
