<?php
namespace delegatr\Interfaces {
    interface Delegate {
        /**
         * Retrieves delegate closure
         * @return \Closure
         */
        public function getClosure();

        /**
         * Retrieves delegate code string
         * @return string
         */
        public function getCode();

        /**
         * Retrieves list of ReflectionParameters if available
         * @return mixed
         */
        public function getParameters();

        /**
         * Retrieves delegate context
         * @return array
         */
        public function getContext();

        /**
         * Alias of \Closure->bindTo
         * @param object $object
         */
        public function bindTo($object);
    }
}