<?php
namespace delegatr {
    use Veval;
    use qtil;
    
    trait Serializable {
        use Delegate;

        /**
         * Standard implementation of delegate serialization behavior
         * @return string
         */
        function serialize() {
            $code = $this->getCode();
            $context = $this->getContext();

            return serialize([
                0 => $code,
                1 => $context 
            ]);
        }

        /**
         * Standard implementation of delegate unserialization behavior
         * @param string $data
         * @return mixed
         * @throws Exception
         */
        function unserialize($data) {
            list($code, $context) = (array)unserialize($data);

            $builder = function() use($code, $context) {
                if(strpos(ini_get('disable_functions'),'eval') === false) {
                    $fn_code = '$_function = '.$code.';';
                    eval($fn_code);
                    return $_function;
                } else {
                    $id = qtil\Identifier::identify($code);
                    $fn_code = 'function delegate_'.$id.'() {
                            return '.$code.';
                        }';
                    Veval::execute('<?php '.$fn_code);
                    return function()use($id) {
                        $closure = call_user_func_array('delegate_'.$id, func_get_args());
                        return $closure();
                    };
                }
            };

            $closure = $builder();
            if (isset($closure) && is_callable($closure)) {
                Registry::add($this, $closure, $context);
            } else {
                throw new Exception('Invalid callback, unpacking delegate failed.');
            }
        }
    }
}

