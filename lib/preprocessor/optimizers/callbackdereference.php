<?php
/**
 *
 *
 * PHP Version 5.2
 *
 * @category
 * @package
 * @subpackage
 */

/**
 *
 *
 * @category   
 * @package
 * @subpackage
 */
class PreProcessor_Optimizers_CallbackDereference extends PreProcessor_Optimizer {

    public function parse(PreProcessor_File $file) {
        $it = new ArrayIterator($file->getTokens());
        $last = '';
        $it->rewind();
        while ($it->valid()) {
            $token = $it->current();
            if (!is_array($token) && $token == '(' && in_array($last, array(')', '}'))) {
                //We found a dereference issue!
                if ($last == '}') {
                    list ($start, $end, $replace) = $this->functionDereference($it);
                } else {
                    list ($start, $end, $replace) = $this->normalDereference($it);
                }
                $array = $it->getArrayCopy();
                array_splice($array, $start, $end - $start, $replace);
//                var_dump($replace, $array);
                $it = new ArrayIterator($array);
                $it->seek($start + count($replace) - 1);
                $last = '';
            }
            if (!is_array($token) || $token[0] != T_WHITESPACE) {
                $last = $token;
            }
            $it->next();
        }
        $file->setTokens($it->getArrayCopy());
    }

    protected function functionDereference(ArrayIterator $it) {
        $pos = $endPos = $it->key();
        $this->seekBackTo($it, '}', '{');
        $this->seekBackTo($it, ')', '(');
        $pos = $it->key();
        do {
            $pos--;
            $it->seek($pos);
            $cur = $it->current();
        } while (is_array($cur) && $cur[0] == T_WHITESPACE);
        if (is_array($cur) && $cur[0] == T_FUNCTION) {
            $replace = array_slice($it->getArrayCopy(), $pos, $endPos - $pos);
            array_unshift(
                $replace,
                array(T_STRING, 'call_user_func', 1),
                '('
            );
            $it->seek($endPos + 1);
            if ($it->current() != ')') {
                $replace[] = ',';
            }
            return array($pos, $endPos + 1, $replace);
        }
    }

    protected function normalDereference(ArrayIterator $it) {
        $strings = array(
            ';',
            '}',
            '{',
            '(',
            '<',
            '>',
            '=',
        );
        $tokens = array(
            T_AND_EQUAL,
            T_ARRAY_CAST,
            T_AS,
            T_BOOLEAN_AND,
            T_BOOLEAN_OR,
            T_BOOL_CAST,
            T_BREAK,
            T_CASE,
            T_CATCH,
            T_CLASS,
            T_CLONE,
            T_CLOSE_TAG,
            T_COMMENT,
            T_CONCAT_EQUAL,
            T_CONST,
            T_CONTINUE,
            T_CURLY_OPEN,
            T_DECLARE,

        );
        $pos = $endPos = $it->key();
        $this->seekBackTo($it, ')', '(');
        $pos = $it->key();
        $go = true;
        $possible = false;
        $possiblePos = 0;
        do {
            $pos--;
            $it->seek($pos);
            $cur = $it->current();
            if (is_array($cur) && ($cur[0] == T_VARIABLE || $cur[0] == T_ARRAY)) {
                $go = false;
            } elseif (is_array($cur) && in_array($cur[0], array(T_STRING))) {
                $possible = true;
                $possiblePos = $it->key();
            } elseif ($possible && ((is_string($cur) && in_array($cur, $strings))|| (is_array($cur) && in_array($cur[0], $tokens)))) {
                $go = false;
                $it->seek($possiblePos);
                $pos = $possiblePos;
            }
        } while ($go);
        $replace = array_slice($it->getArrayCopy(), $pos, $endPos - $pos);
        array_unshift(
            $replace,
            array(T_STRING, 'call_user_func', 1),
            '('
        );
        $it->seek($endPos + 1);
        if ($it->current() != ')') {
            $replace[] = ',';
        }
        return array($pos, $endPos + 1, $replace);
    }

    protected function seekBackTo(ArrayIterator $it, $openChr, $closeChr) {
        $pos = $it->key();
        $open = 0;
        $go = true;
        do {
            $pos--;
            $it->seek($pos);
            if ($it->current() == $openChr) {
                $open++;
                $go = false;
            } elseif ($it->current() == $closeChr) {
                $open--;
            }
        } while ($go || $open > 0);
    }
}
