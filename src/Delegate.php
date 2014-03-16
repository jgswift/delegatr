<?php
namespace delegatr {
    trait Delegate {
        /**
         * Default constructor, any custom constructors must also implement this using Delegate interface
         * @param callable $callback
         * @param array $context
         */
        public function __construct(callable $callback, $context = null) {
            Registry::add($this, $callback, $context);
        }

        /**
         * Provides invoke magic for delegate, emulating closure behavior
         * @return mixed
         */
        public function __invoke() {
            return call_user_func_array($this->getClosure(),func_get_args());
        }

        /**
         * Retrieves closure for delegate
         * @return \Closure
         */
        public function getClosure() {
            return Registry::closure($this);
        }

        /**
         * Retrieves raw closure code using token parsers
         * @return string
         */
        public function getCode() {
            return Registry::code($this);
        }

        /**
         * Retrieves list of ReflectionParameters
         * @return array
         */
        public function getParameters() {
            return Registry::reflect($this)->getParameters();
        }

        /**
         * Retrieves context provided on instantiation
         * @return array
         */
        public function getContext() {
            return Registry::context($this);
        }

        /**
         * Alias to Closure bindTo, rebinding delegate closure
         * @param object $object
         * @return self
         */
        public function bindTo($object) {
            return new self(Registry::bindTo($this, $object),$this->getContext());
        }
    }
}