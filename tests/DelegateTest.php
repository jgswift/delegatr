<?php
namespace delegatr\Tests {
    class DelegateTest extends DelegatrTestCase {
        function testDelegateSerialize() {
            $userAction = new Mock\PredefinedUserAction();
            
            $userActionString = serialize($userAction);
            
            $userAction2 = unserialize($userActionString);
            
            $result = $userAction2();
            
            $this->assertEquals('action executed!',$result);
        }
        
        function testDelegateExecution() {
            $delegate = new Mock\MiscUserAction(function() {
                return 1;
            });
            
            $result = $delegate();
            
            $this->assertEquals(1,$result);
        }
        
        function testDelegateRebinding() {
            $obj1 = new \stdClass();
            $obj1->name = 'object1';
            
            $obj2 = new \stdClass();
            $obj2->name = 'object2';
            
            $delegate = new Mock\MiscUserAction(function() {
                return $this->name;
            });
            
            $delegate->bindTo($obj1);
            
            $result1 = $delegate();
            
            $this->assertEquals('object1',$result1);
            
            $delegate->bindTo($obj2);
            
            $result2 = $delegate();
            
            $this->assertEquals('object2',$result2);
        }
    }
}