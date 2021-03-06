<?php
namespace delegatr\Tests\Mock {
    use delegatr;
    class PredefinedUserAction implements delegatr\Interfaces\Delegate, \Serializable  {
        use delegatr\Serializable {
            delegatr\Serializable::__construct as construct;
        }
        
        function __construct(array $context = []) {
            $this->construct(function() {
                return 'action executed!';
            }, $context);
        }
    }
}