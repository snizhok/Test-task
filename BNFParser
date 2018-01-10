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

    protected $result;
    public $expression;

    public function evaluate($expression)
    {
        $this->expression = preg_replace('/\s+/', '', $expression);
        return $this->expr();
    }

    public function expr()
    {
        $result = $this->term();
        if (!strlen($this->expression)) {
            return $result;
        }
        $add = substr($this->expression, 0, 1);
        if (in_array($add, $this->adds) && strlen($this->expression) > 1) {
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
                throw new Exception('The given expression does not match the BNF');
            }
            return $result;
        }
        throw new Exception('The given expression does not match the BNF');
    }

    protected function term()
    {
        $result = $this->factor();
        if (strlen($this->expression) < 2) {
            return $result;
        }
        $mult = substr($this->expression, 0, 1);
        if (in_array($mult, $this->mults)) {
            $this->expression = substr($this->expression, 1);
            $factor = $this->factor();
            if (!$factor) {
                $factor = $this->term();
            }
            switch ($mult) {
                case '*':
                    $result = $result * $factor;
                    break;
                case '/':
                    $result = $result / $factor;
                    break;
            }
        }
        return $result;

    }

    protected function factor()
    {
        $minusSign = substr($this->expression, 0, 1) == '-';
        if ($minusSign) {
            $this->expression = substr($this->expression, 1);
        }
        return $minusSign ? -1 * $this->neg() : $this->neg();
    }

    protected function neg()
    {
        $lp = substr($this->expression, 0, 1) == '(';
        if ($lp) {
            $pCount = 0;
            for ($i = 0; $i <= strlen($this->expression); $i++) {
                if (!isset($this->expression[$i])) {
                    throw new Exception('The given expression does not match the BNF');
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
            $expressionEnd = substr($this->expression, $i + 1);

            $this->expression = $expression;
            $result = $this->expr();

            $this->expression = $expressionEnd;
            return $result;
        }
        return $this->number();
    }

    public function number()
    {
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
$result = $parser->evaluate('200+(12*((1/8)+1)-19)');
var_dump($result);
