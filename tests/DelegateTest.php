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
        
        function testLambdaExpression() {
            $x = 10;
            $y = 2;
            
            $lambda = new \delegatr\Lambda(function() {
                return $x + $y;
            }, get_defined_vars());
            
            $this->assertEquals(12,$lambda());
        }
        
        function testLambdaSerialize() {
            $lambda = new \delegatr\Lambda(function() {
                return 'foo';
            });
            
            $this->assertEquals('foo',$lambda());
            
            $serial = serialize($lambda);
            
            $lambda2 = unserialize($serial);
            
            $this->assertEquals('foo',$lambda2());
        }
    }
}