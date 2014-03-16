<?php
namespace delegatr {
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
                extract($context);
                eval('$_function = '.$code.';');

                return $_function;
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

