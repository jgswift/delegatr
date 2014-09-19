<?php
namespace delegatr {
    use qtil,FunctionParser;
    
    class Registry {
        /**
         * Global list of delegates by uid
         * @var array
         */
        protected static $delegates = [];
        
        /**
         * List of all delegate UIDs
         * @var array 
         */
        protected static $uids = [];

        /**
         * List of all delegate ReflectionFunctions by uid
         * @var array 
         */
        protected static $reflection = [];
        
        /**
         * List of all delegate context by uid
         * @var array
         */
        protected static $context = [];
        
        /**
         * List of all objects bound by delegates, keyed by uid
         * @var type 
         */
        protected static $object = [];
        
        /**
         * List of FunctionParsers, keyed by delegate uid
         * @var type 
         */
        protected static $parser = [];

        /**
         * Creates function parser for provided delegate
         * @param mixed $object
         * @return FunctionParser\FunctionParser
         */
        static function parse($object) {
            $id = qtil\Identifier::identify($object);
            if(!isset(self::$parser[$id])) {
                self::$parser[$id] = new FunctionParser\FunctionParser(self::$reflection[$id]);
            }

            return self::$parser[$id];
        }

        /**
         * Checks if registry is currently handling class as delegate
         * @param string $class
         * @return boolean
         */
        static function delegates($class) {
            if(is_callable($class)) {
                if(($id = array_search($class, self::$object)) !== false) {
                    return self::$object[$id];
                }

                return false;
            }

            if(is_object($class)) {
                $id = qtil\Identifier::identify( $class );
                if(in_array($id, self::$uids)) {
                    return true;
                }
            }

            return qtil\ReflectorUtil::classUses($class, '\delegatr\Delegate');
        }

        /**
         * Local handling of delegate creation
         * @param mixed $object
         * @param callable $closure
         * @param array|null $context
         */
        static function add($object, callable $closure, array $context = []) {
            $id = qtil\Identifier::identify($object);

            if(is_null($context)) {
                $context = [];
            }
            
            self::$context[$id] = $context;
            self::$delegates[$id] = $closure;
            self::$reflection[$id] = new \ReflectionFunction($closure);
            self::$object[$id] = $object;
            self::$uids[] = $id;
            
            if(!empty($context)) {
                self::rebind($object,$context);
            }
        }

        /**
         * Removal of delegate from object and all corresponding metadata
         * @param mixed $object
         * @throws \InvalidArgumentException
         */
        static function remove($object) {
            if(is_callable($object)) {
                if(($id = in_array($object, self::$delegates)) !== false) {
                    $object = $id;
                }
            }
            
            if(is_object($object)) {
                $object = qtil\Identifier::identify($object);
            }
            
            if(is_string($object)) {
                self::$context[$object] =
                self::$reflection[$object] =
                self::$object[$object]  =
                self::$delegates[$object] = null;
                if(($k = array_search($object, self::$uids)) !== false) {
                    self::$uids[$k] = null;
                }
            } else {
                throw new \InvalidArgumentException();
            }
        }

        /**
         * Retrieves closure for delegate
         * @param mixed $object
         * @return mixed
         */
        static function closure($object) {
            if(self::delegates($object))
            {
                $id = qtil\Identifier::identify($object);
                return self::$delegates[$id];
            }
        }

        /**
         * Performs rebinding of delegate closure
         * @param mixed $object
         * @param mixed $subject
         * @return \Closure
         */
        static function bindTo($object, $subject) {
            if(self::delegates($object)) {
                $id = qtil\Identifier::identify($object);
                if(method_exists(self::$delegates[$id],'bindTo')) {
                    self::$delegates[$id] = self::$delegates[$id]->bindTo($subject,$subject);
                }
                self::$reflection[$id] = new \ReflectionFunction(self::$delegates[$id]);
                self::$parser[$id] = new FunctionParser\FunctionParser(self::$reflection[$id]);
                return self::$delegates[$id];
            }
            
            return $object;
        }

        /**
         * Retrieves local ReflectionFunction for delegate
         * @param mixed $object
         * @return \ReflectionFunction
         */
        static function reflect($object) {
            if(self::delegates($object)) {
                $id = qtil\Identifier::identify($object);

                return self::$reflection[$id];
            }
        }

        /**
         * Retrieves delegate code string
         * @param mixed $object
         * @return string
         */
        static function code($object) {
            $parser = self::parse($object);
            return $parser->getCode();
        }

        /**
         * Retrieves delegate context
         * @param mixed $object
         * @return array
         */
        static function context($object) {
            $parser = self::parse($object);
            return $parser->getContext();
        }
        
        /**
         * Dynamically adds context to closure
         * @param mixed $object
         * @param array $context
         */
        static function rebind($object,array $context) {
            $code = self::code($object);
            $count = count($context)-1;
            
            $fn_code = '$_function = '.$code.';';
            if(($pos = strpos($fn_code,'use(')) !== false) {
                $c_string = '';
                foreach($context as $k => $c) {
                    $c_string.='$'.$k.',';
                }
                
                $fn_code = substr($fn_code,0,$pos).$c_string.substr($fn_code,$pos);
            } else {
                $pos = strpos($fn_code,'{');
                if($pos !== false) {
                    $pos -= 1;
                    $c_string = '';
                    $i = 0;
                    foreach($context as $k => $c) {
                        $c_string.='$'.$k;
                        if($i < $count) {
                            $c_string.=',';
                        }
                        $i++;
                    }
                    
                    $fn_code = substr($fn_code,0,$pos).'use('.$c_string.')'.substr($fn_code,$pos);
                }
            }
            
            if(strpos(ini_get('disable_functions'),'eval') === false) {
                extract($context);
                eval($fn_code);
            } else {
                $id = qtil\Identifier::identify($code);
                
                $a_string = '';
                $i = 0;
                
                foreach($context as $k => $c) {
                    $a_string.='$'.$k;
                    if($i < $count) {
                        $a_string.=',';
                    }
                    $i++;
                }
                
                $fn_code = 
                    'function delegate_'.$id.'('.$a_string.') {
                        return '.$fn_code.';
                    }';
                
                \Veval::execute('<?php '.$fn_code);
                
                $_function = call_user_func_array('delegate_'.$id, array_values($context));
            }
            
            self::add($object, $_function);
        }
    }
}